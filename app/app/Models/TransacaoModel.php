<?php

namespace App\Models;

use CodeIgniter\Model;

class TransacaoModel extends Model
{
    protected $table = 'transacoes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tipo', 'valor', 'data_vencimento', 'data_caixa', 'status', 'descricao', 'categoria_id', 'pessoa_id'];
    protected $useTimestamps = true;

    // ... (Regras de validação e mensagens omitidas para brevidade) ...

    protected $validationRules = [
        'id'               => 'permit_empty|is_natural_no_zero',
        'tipo'             => 'required|in_list[PAGAR,RECEBER]',
        'valor'            => 'required|numeric|greater_than[0]',
        'data_vencimento'  => 'required|valid_date',
        'descricao'        => 'required|max_length[255]',
        'categoria_id'     => 'required|is_natural_no_zero',
        'pessoa_id'        => 'required|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'valor' => [
            'required' => 'O campo Valor é obrigatório.',
            'numeric' => 'O Valor deve ser um número.',
            'greater_than' => 'O Valor deve ser maior que zero.',
        ],
        'tipo' => [
            'in_list' => 'O Tipo deve ser "PAGAR" ou "RECEBER".',
        ],
        'categoria_id' => [
            'required' => 'Selecione uma Categoria Financeira.',
        ],
        'pessoa_id' => [
            'required' => 'Selecione a Pessoa (Cliente/Fornecedor).',
        ],
    ];

    /**
     * Calcula o saldo líquido de caixa acumulado até o dia anterior à data de início do relatório.
     *
     * @param string $dataInicio Data de início do relatório (YYYY-MM-DD).
     * @return float Saldo acumulado (pode ser positivo ou negativo).
     */
    public function getSaldoInicial(string $dataInicio): float
    {
        $builder = $this->db->table('transacoes');

        // Limpa a vírgula (,) e espaços antes de somar.
        $selectValue = "CAST(REPLACE(TRIM(valor), ',', '.') AS DECIMAL(10,2))";

        // Consulta única e robusta
        $builder->select(
            "COALESCE(SUM(IF(TRIM(UPPER(tipo)) = 'RECEBER', {$selectValue}, 0)), 0) as entradas, " .
                "COALESCE(SUM(IF(TRIM(UPPER(tipo)) = 'PAGAR', {$selectValue}, 0)), 0) as saidas",
            false
        );

        // FILTROS
        $builder->where('data_caixa <', $dataInicio);
        $builder->where("TRIM(UPPER(status)) = 'CONCLUIDA'", null, false);

        $resultado = $builder->get()->getRow();

        if ($resultado) {
            // Se o MySQL retornou o valor, o PHP deve ler a string DECIMAL e converter para float
            $entradas = (float) strval($resultado->entradas);
            $saidas = (float) strval($resultado->saidas);

            // Log final para inspecionar o valor lido pelo PHP
            // log_message('error', 'Lido pelo PHP: Entradas=' . $entradas . ', Saidas=' . $saidas);

            return $entradas - $saidas;
        }

        return 0.00;
    }


    /**
     * Busca os dados para o relatório no período especificado,
     * incluindo o nome da categoria e o tipo de fluxo.
     *
     * @param string $dataInicio Data de início (YYYY-MM-DD).
     * @param string $dataFim Data de fim (YYYY-MM-DD).
     * @return array
     */
    public function getRelatorio(string $dataInicio, string $dataFim): array
    {
        return $this->select('
                transacoes.*,
                cf.nome as categoria_nome,
                cf.tipo_fluxo as tipo_fluxo_categoria
            ')
            ->join('categorias_financeiras cf', 'cf.id = transacoes.categoria_id', 'left')
            ->where("TRIM(UPPER(transacoes.status)) = 'CONCLUIDA'", null, false)
            ->where('transacoes.data_caixa >=', $dataInicio)
            ->where('transacoes.data_caixa <=', $dataFim)
            ->orderBy('transacoes.data_caixa', 'ASC')
            ->findAll();
    }


    /**
     * Gera o relatório consolidado da DFC (Demonstração do Fluxo de Caixa) para um período.
     * ... (código mantido) ...
     */
    public function gerarRelatorioDFC(string $dataInicio, string $dataFim): array
    {
        $fco = $this->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCO');
        $fci = $this->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCI');
        $fcf = $this->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCF');

        $total_liquido = ($fco['liquido'] ?? 0) + ($fci['liquido'] ?? 0) + ($fcf['liquido'] ?? 0);

        $dfc = [
            'FCO' => $fco,
            'FCI' => $fci,
            'FCF' => $fcf,
            'TOTAL' => $total_liquido
        ];

        return $dfc;
    }

    /**
     * Calcula o fluxo de caixa líquido (Entradas - Saídas) para um tipo de fluxo DFC específico ('FCO', 'FCI', 'FCF') em um período.
     *
     * @param string $dataInicio Data inicial do período (YYYY-MM-DD).
     * @param string $dataFim Data final do período (YYYY-MM-DD).
     * @param string $tipoFluxo Tipo de Fluxo (FCO, FCI, FCF).
     * @return array Contendo 'entrada', 'saida' e 'liquido' (singular).
     */
    public function getFluxoLiquidoPorTipo(string $dataInicio, string $dataFim, string $tipoFluxo): array
    {
        $builder = $this->db->table('transacoes t');

        // Limpa a vírgula (,) e espaços antes de somar.
        $selectValue = "CAST(REPLACE(TRIM(t.valor), ',', '.') AS DECIMAL(10,2))";

        // MUDANÇA CRÍTICA: Usando IF e comparação com operador de igualdade
        $builder->select(
            "COALESCE(SUM(IF(TRIM(UPPER(t.tipo)) = 'RECEBER', {$selectValue}, 0)), 0) as entradas, " .
                "COALESCE(SUM(IF(TRIM(UPPER(t.tipo)) = 'PAGAR', {$selectValue}, 0)), 0) as saidas",
            false
        );

        $builder->join('categorias_financeiras c', 't.categoria_id = c.id');
        // Filtro de Status (CORREÇÃO DE SINTAXE)
        $builder->where("TRIM(UPPER(t.status)) = 'CONCLUIDA'", null, false);
        $builder->where('c.tipo_fluxo', $tipoFluxo);
        $builder->where('t.data_caixa >=', $dataInicio);
        $builder->where('t.data_caixa <=', $dataFim);

        $resultado = $builder->get()->getRow();

        if (!$resultado) {
            log_message('error', 'Falha na consulta DFC para o tipo: ' . $tipoFluxo);
            return ['entrada' => 0, 'saida' => 0, 'liquido' => 0];
        }

        $entradas_sql = (float) strval($resultado->entradas);
        $saidas_sql   = (float) strval($resultado->saidas);

        return [
            'entrada' => $entradas_sql,
            'saida'   => $saidas_sql,
            'liquido' => $entradas_sql - $saidas_sql,
        ];
    }
}

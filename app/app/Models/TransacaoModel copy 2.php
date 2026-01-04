<?php

namespace App\Models;

use CodeIgniter\Model;

class TransacaoModel extends Model
{
    protected $table = 'transacoes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tipo', 'valor', 'data_vencimento', 'data_caixa', 'status', 'descricao', 'categoria_id', 'pessoa_id'];
    protected $useTimestamps = true;

    // Adicionando as regras de validação que o Controller espera
    // protected $validationRules = [
    //     'id'               => 'permit_empty|is_natural_no_zero',
    //     'tipo'             => 'required|in_list[PAGAR,RECEBER]',
    //     'valor'            => 'required|numeric|greater_than[0]',
    //     'data_vencimento'  => 'required|valid_date',
    //     'descricao'        => 'required|max_length[255]',
    //     'categoria_id'     => 'required|is_natural_no_zero',
    //     'pessoa_id'        => 'required|is_natural_no_zero',
    // ];

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
    // public function getSaldoInicial(string $dataInicio): float
    // {
    //     $builder = $this->db->table('transacoes');
    //     // CORREÇÃO MÁXIMA: Uso de TRIM(UPPER(tipo)) para garantir que a comparação funcione
    //     $builder->select('
    //         COALESCE(SUM(CASE WHEN TRIM(UPPER(tipo)) = \'RECEBER\' THEN valor ELSE 0 END), 0) as entradas,
    //         COALESCE(SUM(CASE WHEN TRIM(UPPER(tipo)) = \'PAGAR\' THEN valor ELSE 0 END), 0) as saidas
    //     ', false);

    //     // CRUCIAL: Seleciona todas as transações concluídas cuja data_caixa é ANTERIOR (<) à data de início.
    //     $builder->where('data_caixa <', $dataInicio);
    //     // $builder->where('status', 'CONCLUIDA');
    //     $builder->where('TRIM(UPPER(status))', 'CONCLUIDA', false);

    //     $resultado = $builder->get()->getRow();

    //     if ($resultado) {
    //         // O saldo é Entradas - Saídas.
    //         return (float) $resultado->entradas - (float) $resultado->saidas;
    //     }

    //     return 0.00;
    // }

    /**
     * Calcula o saldo líquido de caixa acumulado até o dia anterior à data de início do relatório.
     *
     * @param string $dataInicio Data de início do relatório (YYYY-MM-DD).
     * @return float Saldo acumulado (pode ser positivo ou negativo).
     */
    public function getSaldoInicial(string $dataInicio): float
    {
        $builder = $this->db->table('transacoes');

        // Uso de CAST e TRIM(UPPER) no tipo
        $builder->select('
            COALESCE(SUM(CASE WHEN TRIM(UPPER(tipo)) = \'RECEBER\' THEN CAST(valor AS DECIMAL(10,2)) ELSE 0 END), 0) as entradas,
            COALESCE(SUM(CASE WHEN TRIM(UPPER(tipo)) = \'PAGAR\' THEN CAST(valor AS DECIMAL(10,2)) ELSE 0 END), 0) as saidas
        ', false);

        // CORREÇÃO CRÍTICA DE DATA E STATUS:

        // 1. Filtro de Data (STR_TO_DATE é a solução mais segura se o formato não for YYYY-MM-DD)
        // Se a data estiver no formato DD/MM/YYYY, use: STR_TO_DATE(data_caixa, \'%d/%m/%Y\')
        // Se a data estiver no formato YYYY-MM-DD (padrão MySQL), a próxima linha será suficiente.
        // Vamos usar a forma mais pura para não causar problemas com a data que você já envia em YYYY-MM-DD

        // A data é armazenada como YYYY-MM-DD, vamos usar o WHERE RAW
        $builder->where("data_caixa < '{$dataInicio}'", null, false);

        // Se a consulta falhou no diagnóstico, o problema está no valor NULL/formato errado
        // Vamos forçar a comparação de um jeito que funcione se o campo estiver com NULL ou string vazia:
        $builder->where('data_caixa IS NOT NULL');
        $builder->where('data_caixa !=', '');

        // 2. Filtro de Status
        $builder->where('TRIM(UPPER(status))', 'CONCLUIDA', false);

        $resultado = $builder->get()->getRow();

        if ($resultado) {
            return (float) $resultado->entradas - (float) $resultado->saidas;
        }

        return 0.00;
    }


    /**
     * Busca os dados para o relatório no período especificado,
     * incluindo o nome da categoria e o tipo de fluxo.
     *
     * @param string $dataInicio Data de início (YYYY-MM-DD)
     * @param string $dataFim Data de fim (YYYY-MM-DD)
     * @return array
     */
    public function gerarRelatorioDFC(string $dataInicio, string $dataFim): array
    {
        return $this->select('
                transacoes.*,
                cf.nome as categoria_nome,
                cf.tipo_fluxo as tipo_fluxo_categoria
            ')
            ->join('categorias_financeiras cf', 'cf.id = transacoes.categoria_id', 'left')

            // O filtro deve ser feito pelo campo data_caixa, crucial para o DFC
            ->where('transacoes.data_caixa >=', $dataInicio)
            ->where('transacoes.data_caixa <=', $dataFim)
            ->orderBy('transacoes.data_caixa', 'ASC')
            ->findAll();
    }


    /**
     * Gera o relatório consolidado da DFC (Demonstração do Fluxo de Caixa) para um período.
     * Utiliza o método getFluxoLiquidoPorTipo para calcular FCO, FCI e FCF.
     * @param string $dataInicio
     * @param string $dataFim
     * @return array
     */
    // public function gerarRelatorioDFC(string $dataInicio, string $dataFim): array
    // {
    //     $fco = $this->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCO');
    //     $fci = $this->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCI');
    //     $fcf = $this->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCF');

    //     $total_liquido = ($fco['liquido'] ?? 0) + ($fci['liquido'] ?? 0) + ($fcf['liquido'] ?? 0);

    //     // Formata os resultados para facilitar a exibição
    //     $dfc = [
    //         'FCO' => $fco,
    //         'FCI' => $fci,
    //         'FCF' => $fcf,
    //         'TOTAL' => $total_liquido
    //     ];

    //     return $dfc;
    // }


    public function getRelatorio(string $dataInicio, string $dataFim): array
    {
        return $this->select('
                transacoes.*,
                cf.nome as categoria_nome,
                cf.tipo_fluxo as tipo_fluxo_categoria
            ')
            ->join('categorias_financeiras cf', 'cf.id = transacoes.categoria_id', 'left')

            // CORREÇÃO DE FILTRO AQUI TAMBÉM: Garante que datas NULL e vazias sejam ignoradas
            ->where('data_caixa IS NOT NULL')
            ->where('data_caixa !=', '')
            ->where('transacoes.data_caixa >=', $dataInicio)
            ->where('transacoes.data_caixa <=', $dataFim)
            ->orderBy('transacoes.data_caixa', 'ASC')
            ->findAll();
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

        // CORREÇÃO MÁXIMA: Uso de TRIM(UPPER(t.tipo))
        $builder->select(
            "COALESCE(SUM(CASE WHEN TRIM(UPPER(t.tipo)) = 'RECEBER' THEN t.valor ELSE 0 END), 0) as entradas, " .
                "COALESCE(SUM(CASE WHEN TRIM(UPPER(t.tipo)) = 'PAGAR' THEN t.valor ELSE 0 END), 0) as saidas",
            false
        );

        $builder->join('categorias_financeiras c', 't.categoria_id = c.id');
        $builder->where('t.status', 'CONCLUIDA');
        $builder->where('c.tipo_fluxo', $tipoFluxo);
        $builder->where('t.data_caixa >=', $dataInicio);
        $builder->where('t.data_caixa <=', $dataFim);

        $resultado = $builder->get()->getRow();

        if (!$resultado) {
            log_message('error', 'Falha na consulta DFC para o tipo: ' . $tipoFluxo);
            return ['entrada' => 0, 'saida' => 0, 'liquido' => 0];
        }

        $entradas_sql = (float) $resultado->entradas;
        $saidas_sql   = (float) $resultado->saidas;

        return [
            'entrada' => $entradas_sql,
            'saida'   => $saidas_sql,
            'liquido' => $entradas_sql - $saidas_sql,
        ];
    }
}

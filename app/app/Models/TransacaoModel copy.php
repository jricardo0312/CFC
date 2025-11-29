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
     * Gera o relatório consolidado da DFC (Fluxo de Caixa) para um período.
     * Utiliza o método getFluxoLiquidoPorTipo para calcular FCO, FCI e FCF.
     * @param string $dataInicio
     * @param string $dataFim
     * @return array
     */
    public function gerarRelatorioDFC(string $dataInicio, string $dataFim): array
    {
        // Este método está CORRETO.
        $fco = $this->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCO');
        $fci = $this->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCI');
        $fcf = $this->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCF');

        $total_liquido = ($fco['liquido'] ?? 0) + ($fci['liquido'] ?? 0) + ($fcf['liquido'] ?? 0);

        // Formata os resultados para facilitar a exibição
        $dfc = [
            'FCO' => $fco,
            'FCI' => $fci,
            'FCF' => $fcf,
            'TOTAL' => $total_liquido
        ];

        return $dfc;
    }

    /**
     * [MÉTODO CORRIGIDO E OTIMIZADO]
     *
     * Calcula o fluxo de caixa líquido (Entradas - Saídas) para um tipo de fluxo DFC específico ('FCO', 'FCI', 'FCF') em um período.
     * Utiliza os nomes de coluna que você forneceu.
     *
     * @param string $dataInicio Data inicial do período (YYYY-MM-DD).
     * @param string $dataFim Data final do período (YYYY-MM-DD).
     * @param string $tipoFluxo Tipo de Fluxo (FCO, FCI, FCF).
     * @return array Contendo 'entrada', 'saida' e 'liquido' (singular).
     */
    public function getFluxoLiquidoPorTipo(string $dataInicio, string $dataFim, string $tipoFluxo): array
    {
        $builder = $this->db->table('transacoes t');

        $builder->select(
            "COALESCE(SUM(CASE WHEN t.tipo = 'RECEBER' THEN t.valor ELSE 0 END), 0) as entradas, " . // O alias do SQL (plural)
                "COALESCE(SUM(CASE WHEN t.tipo = 'PAGAR' THEN t.valor ELSE 0 END), 0) as saidas",   // O alias do SQL (plural)
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
            // Retorna com as chaves no SINGULAR
            return ['entrada' => 0, 'saida' => 0, 'liquido' => 0];
        }

        // As propriedades do $resultado (do SQL) vêm no plural
        $entradas_sql = (float) $resultado->entradas;
        $saidas_sql   = (float) $resultado->saidas;

        // --- CORREÇÃO AQUI ---
        // Retorna o array com as chaves no SINGULAR, como a View espera
        return [
            'entrada' => $entradas_sql,
            'saida'   => $saidas_sql,
            'liquido' => $entradas_sql - $saidas_sql,
        ];
    }
    // --------------------------------------------------------------------
    // MÉTODO ADICIONADO (O QUE O CONTROLLER PRECISA)
    // --------------------------------------------------------------------


}

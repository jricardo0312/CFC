<?php

namespace App\Models;

use CodeIgniter\Model;

class TransacaoModel extends Model
{
    protected $table = 'transacoes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tipo',
        'valor',
        'data_vencimento',
        'data_caixa',
        'status',
        'descricao',
        'categoria_id',
        'pessoa_id'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Regras de validação
    protected $validationRules = [
        'tipo'             => 'required|in_list[PAGAR,RECEBER]',
        'valor'            => 'required|numeric',
        'data_vencimento'  => 'required|valid_date',
        'status'           => 'required|in_list[PENDENTE,CONCLUIDA]',
        'categoria_id'     => 'required|is_natural_no_zero',
        'pessoa_id'        => 'required|is_natural_no_zero',
        // data_caixa é obrigatória APENAS se o status for CONCLUIDA
        'data_caixa'       => 'permit_empty|valid_date',
    ];

    /**
     * Retorna o valor líquido do fluxo de caixa para um período e tipo de fluxo específicos.
     * @param string $dataInicio
     * @param string $dataFim
     * @param string $tipoFluxo (FCO, FCI, FCF)
     * @return array
     */
    public function getFluxoLiquidoPorTipo(string $dataInicio, string $dataFim, string $tipoFluxo): array
    {
        return $this->select("
                SUM(CASE WHEN t.tipo = 'RECEBER' THEN t.valor ELSE 0 END) AS entradas,
                SUM(CASE WHEN t.tipo = 'PAGAR' THEN t.valor ELSE 0 END) AS saidas,
                (SUM(CASE WHEN t.tipo = 'RECEBER' THEN t.valor ELSE 0 END) - SUM(CASE WHEN t.tipo = 'PAGAR' THEN t.valor ELSE 0 END)) AS liquido
            ")
            ->from('transacoes t')
            ->join('categorias_financeiras c', 't.categoria_id = c.id')
            ->where('t.status', 'CONCLUIDA')
            ->where('c.tipo_fluxo', $tipoFluxo)
            ->where('t.data_caixa >=', $dataInicio)
            ->where('t.data_caixa <=', $dataFim)
            ->first();
    }
}

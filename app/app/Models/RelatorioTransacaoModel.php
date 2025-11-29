<?php

namespace App\Models;

use CodeIgniter\Model;

// --------------------------------------------------------------------
// MÉTODO PARA O RELATÓRIO DE TRANSAÇÕES
// --------------------------------------------------------------------

class RelatorioTransacaoModel extends Model
{
    protected $table = 'transacoes';
    protected $primaryKey = 'id';

    // Colunas que podem ser modificadas (para uso em outras operações CRUD)
    protected $allowedFields = [
        'tipo',
        'valor',
        'data_vencimento',
        'data_caixa',
        'status',
        'descricao',
        'categoria_id',
        'pessoa_id',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Busca os dados para o relatório no período especificado,
     * incluindo o nome da categoria e da pessoa (cliente/fornecedor).
     *
     * @param string $dataInicio Data de início (YYYY-MM-DD)
     * @param string $dataFim Data de fim (YYYY-MM-DD)
     * @return array
     */
    public function getRelatorio(string $dataInicio, string $dataFim): array
    {
        return $this->select('
                transacoes.*,
                cf.nome as categoria_nome,
                p.nome as pessoa_nome
            ')
            ->join('categorias_financeiras cf', 'cf.id = transacoes.categoria_id', 'left')
            ->join('clientes_fornecedores p', 'p.id = transacoes.pessoa_id', 'left')
            // O filtro deve ser feito pelo campo data_caixa, crucial para o DFC
            ->where('transacoes.data_caixa >=', $dataInicio)
            ->where('transacoes.data_caixa <=', $dataFim)
            ->orderBy('transacoes.data_caixa', 'ASC')
            ->findAll();
    }
}

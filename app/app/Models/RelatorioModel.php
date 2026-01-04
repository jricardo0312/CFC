<?php

namespace App\Models;

use CodeIgniter\Model;

class RelatorioModel extends Model
{
    protected $table = 'transacoes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['valor', 'tipo', 'data_caixa', 'pessoa_id'];

    // Coluna de relacionamento FIXA (já sabemos que é pessoa_id)
    private $colunaRelacionamento = 'pessoa_id';

    /**
     * Obtém transações com filtros
     */
    public function getTransacoesPorTipoCliente($dataInicio = null, $dataFim = null, $tipoCliente = null, $tipoTransacao = null)
    {
        $builder = $this->db->table('transacoes t');
        $builder->select('t.*, p.nome, p.tipo_cliente, p.email');
        $builder->join('pessoas p', 'p.id = t.pessoa_id'); // CORREÇÃO: pessoa_id

        // Filtro por datas (usando data_caixa conforme sua estrutura)
        if (!empty($dataInicio)) {
            $builder->where('DATE(t.data_caixa) >=', date('Y-m-d', strtotime($dataInicio)));
        }

        if (!empty($dataFim)) {
            $builder->where('DATE(t.data_caixa) <=', date('Y-m-d', strtotime($dataFim)));
        }

        // Filtro por tipo de cliente
        if (!empty($tipoCliente) && $tipoCliente != 'todos') {
            $builder->where('p.tipo_cliente', $tipoCliente);
        }

        // Filtro por tipo de transação (PAGAR/RECEBER)
        if (!empty($tipoTransacao) && $tipoTransacao != 'todos') {
            $builder->where('t.tipo', $tipoTransacao);
        }

        // Filtro por status (opcional, se quiser apenas concluídas)
        // $builder->where('t.status', 'CONCLUIDA');

        $builder->orderBy('t.data_caixa', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Obtém totais por tipo de cliente
     */
    public function getTotalPorTipoCliente($dataInicio = null, $dataFim = null, $tipoCliente = null, $tipoTransacao = null)
    {
        $builder = $this->db->table('transacoes t');
        $builder->select('SUM(t.valor) as total, p.tipo_cliente, t.tipo, COUNT(t.id) as quantidade');
        $builder->join('pessoas p', 'p.id = t.pessoa_id');

        if (!empty($dataInicio)) {
            $builder->where('DATE(t.data_caixa) >=', date('Y-m-d', strtotime($dataInicio)));
        }

        if (!empty($dataFim)) {
            $builder->where('DATE(t.data_caixa) <=', date('Y-m-d', strtotime($dataFim)));
        }

        if (!empty($tipoCliente) && $tipoCliente != 'todos') {
            $builder->where('p.tipo_cliente', $tipoCliente);
        }

        if (!empty($tipoTransacao) && $tipoTransacao != 'todos') {
            $builder->where('t.tipo', $tipoTransacao);
        }

        $builder->groupBy('p.tipo_cliente, t.tipo');

        return $builder->get()->getResultArray();
    }

    /**
     * Obtém tipos de cliente distintos
     */
    public function getTiposCliente()
    {
        $builder = $this->db->table('pessoas');
        $builder->select('tipo_cliente')->distinct();
        $builder->where('tipo_cliente IS NOT NULL');
        $builder->where("tipo_cliente != ''");
        $builder->orderBy('tipo_cliente', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Obtém tipos de transação distintos
     */
    public function getTiposTransacao()
    {
        $builder = $this->db->table('transacoes');
        $builder->select('tipo')->distinct();
        $builder->where('tipo IS NOT NULL');
        $builder->where("tipo != ''");
        $builder->orderBy('tipo', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Obtém resumo geral
     */
    public function getResumoGeral($dataInicio = null, $dataFim = null, $tipoCliente = null, $tipoTransacao = null)
    {
        $builder = $this->db->table('transacoes t');
        $builder->select("
            SUM(CASE WHEN t.tipo = 'RECEBER' THEN t.valor ELSE 0 END) as total_receber,
            SUM(CASE WHEN t.tipo = 'PAGAR' THEN t.valor ELSE 0 END) as total_pagar,
            SUM(CASE WHEN t.tipo = 'RECEBER' THEN t.valor ELSE -t.valor END) as saldo,
            COUNT(t.id) as quantidade_total
        ");
        $builder->join('pessoas p', 'p.id = t.pessoa_id');

        if (!empty($dataInicio)) {
            $builder->where('DATE(t.data_caixa) >=', date('Y-m-d', strtotime($dataInicio)));
        }

        if (!empty($dataFim)) {
            $builder->where('DATE(t.data_caixa) <=', date('Y-m-d', strtotime($dataFim)));
        }

        if (!empty($tipoCliente) && $tipoCliente != 'todos') {
            $builder->where('p.tipo_cliente', $tipoCliente);
        }

        if (!empty($tipoTransacao) && $tipoTransacao != 'todos') {
            $builder->where('t.tipo', $tipoTransacao);
        }

        $result = $builder->get()->getRowArray();

        return [
            'total_receber' => $result['total_receber'] ?? 0,
            'total_pagar' => $result['total_pagar'] ?? 0,
            'saldo' => $result['saldo'] ?? 0,
            'quantidade_total' => $result['quantidade_total'] ?? 0
        ];
    }

    /**
     * Método de teste rápido
     */
    public function testarConsulta()
    {
        $builder = $this->db->table('transacoes t');
        $builder->select('t.id, t.tipo, t.valor, t.data_caixa, p.nome, p.tipo_cliente');
        $builder->join('pessoas p', 'p.id = t.pessoa_id');
        $builder->limit(5);

        return $builder->get()->getResultArray();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class RelatorioModel extends Model
{
    protected $table = 'transacoes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pessoa_id', 'valor', 'data_caixa'];

    public function getTransacoesPorTipoCliente($dataInicio = null, $dataFim = null, $tipoCliente = null)
    {
        $builder = $this->db->table('transacoes t');
        $builder->select('t.*, p.nome, p.tipo_cliente, p.email');
        $builder->join('pessoas p', 'p.id = t.id');

        // Filtro por datas
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

        $builder->orderBy('t.data_caixa', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function getTotalPorTipoCliente($dataInicio = null, $dataFim = null, $tipoCliente = null)
    {
        $builder = $this->db->table('transacoes t');
        $builder->select('SUM(t.valor) as total, p.tipo_cliente');
        $builder->join('pessoas p', 'p.id = t.id');

        if (!empty($dataInicio)) {
            $builder->where('DATE(t.data_caixa) >=', date('Y-m-d', strtotime($dataInicio)));
        }

        if (!empty($dataFim)) {
            $builder->where('DATE(t.data_caixa) <=', date('Y-m-d', strtotime($dataFim)));
        }

        if (!empty($tipoCliente) && $tipoCliente != 'todos') {
            $builder->where('p.tipo_cliente', $tipoCliente);
        }

        $builder->groupBy('p.tipo_cliente');

        return $builder->get()->getResultArray();
    }

    public function getTiposCliente()
    {
        $builder = $this->db->table('pessoas');
        $builder->select('tipo_cliente')->distinct()->where('tipo_cliente IS NOT NULL');
        $builder->orderBy('tipo_cliente', 'ASC');

        return $builder->get()->getResultArray();
    }
}

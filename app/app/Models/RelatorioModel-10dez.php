<?php

namespace App\Models;

use CodeIgniter\Model;

class RelatorioModel extends Model
{
    protected $table = 'transacoes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['valor', 'tipo', 'data_caixa'];

    // Nome da coluna de relacionamento (será detectado automaticamente)
    private $colunaRelacionamento = null;

    /**
     * Detecta a coluna de relacionamento entre transacoes e pessoas
     */
    private function detectarColunaRelacionamento()
    {
        // Se já detectou, retorna
        if ($this->colunaRelacionamento !== null) {
            return $this->colunaRelacionamento;
        }

        $db = \Config\Database::connect();

        // Primeiro, tenta descobrir pela estrutura da tabela
        $fields = $db->getFieldData('transacoes');

        // Lista de possíveis nomes (em ordem de probabilidade)
        $possiveisNomes = [
            'pessoa_id',
            'id_pessoa',
            'cliente_id',
            'id_cliente',
            'fk_pessoa',
            'fk_cliente',
            'pessoa',
            'cliente'
        ];

        foreach ($fields as $field) {
            if (in_array($field->name, $possiveisNomes)) {
                $this->colunaRelacionamento = $field->name;
                log_message('info', "Coluna de relacionamento detectada: {$field->name}");
                return $this->colunaRelacionamento;
            }
        }

        // Se não encontrou pelos nomes exatos, procura por substring
        foreach ($fields as $field) {
            foreach ($possiveisNomes as $nome) {
                if (stripos($field->name, $nome) !== false || stripos($nome, $field->name) !== false) {
                    $this->colunaRelacionamento = $field->name;
                    log_message('info', "Coluna de relacionamento detectada por substring: {$field->name}");
                    return $this->colunaRelacionamento;
                }
            }
        }

        // Se ainda não encontrou, tenta pelo primeiro campo que parece ser FK
        foreach ($fields as $field) {
            if (
                strpos($field->name, '_id') !== false ||
                strpos($field->name, 'id_') !== false ||
                strpos($field->name, 'fk_') !== false
            ) {
                $this->colunaRelacionamento = $field->name;
                log_message('info', "Coluna de relacionamento detectada por padrão: {$field->name}");
                return $this->colunaRelacionamento;
            }
        }

        // Valor padrão se não encontrar
        $this->colunaRelacionamento = 'pessoa_id';
        log_message('warning', "Coluna de relacionamento não detectada, usando padrão: pessoa_id");
        return $this->colunaRelacionamento;
    }

    /**
     * Obtém transações com filtros
     */
    public function getTransacoesPorTipoCliente($dataInicio = null, $dataFim = null, $tipoCliente = null, $tipoTransacao = null)
    {
        $colunaRel = $this->detectarColunaRelacionamento();
        $colunaData = $this->detectarColunaData(); // Método novo para detectar coluna de data

        $builder = $this->db->table('transacoes t');
        $builder->select('t.*, p.nome, p.tipo_cliente, p.email');
        $builder->join('pessoas p', "p.id = t.{$colunaRel}");

        // Filtro por datas
        if (!empty($dataInicio)) {
            $builder->where("DATE(t.{$colunaData}) >=", date('Y-m-d', strtotime($dataInicio)));
        }

        if (!empty($dataFim)) {
            $builder->where("DATE(t.{$colunaData}) <=", date('Y-m-d', strtotime($dataFim)));
        }

        // Filtro por tipo de cliente
        if (!empty($tipoCliente) && $tipoCliente != 'todos') {
            $builder->where('p.tipo_cliente', $tipoCliente);
        }

        // Filtro por tipo de transação (PAGAR/RECEBER)
        if (!empty($tipoTransacao) && $tipoTransacao != 'todos') {
            $builder->where('t.tipo', $tipoTransacao);
        }

        $builder->orderBy("t.{$colunaData}", 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Detecta a coluna de data
     */
    private function detectarColunaData()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldData('transacoes');

        // Possíveis nomes de coluna de data
        $possiveisNomes = ['data_caixa', 'data_transacao', 'data', 'created_at', 'dt_criacao', 'data_criacao'];

        foreach ($fields as $field) {
            if (in_array($field->name, $possiveisNomes)) {
                return $field->name;
            }
        }

        // Procura por substring
        foreach ($fields as $field) {
            foreach ($possiveisNomes as $nome) {
                if (stripos($field->name, $nome) !== false) {
                    return $field->name;
                }
            }
        }

        // Procura por campos do tipo data/datetime/timestamp
        foreach ($fields as $field) {
            $type = strtolower($field->type);
            if (
                strpos($type, 'date') !== false ||
                strpos($type, 'time') !== false ||
                strpos($type, 'timestamp') !== false
            ) {
                return $field->name;
            }
        }

        // Padrão
        return 'created_at';
    }

    /**
     * Obtém totais por tipo de cliente
     */
    public function getTotalPorTipoCliente($dataInicio = null, $dataFim = null, $tipoCliente = null, $tipoTransacao = null)
    {
        $colunaRel = $this->detectarColunaRelacionamento();
        $colunaData = $this->detectarColunaData();

        $builder = $this->db->table('transacoes t');
        $builder->select('SUM(t.valor) as total, p.tipo_cliente, t.tipo, COUNT(t.id) as quantidade');
        $builder->join('pessoas p', "p.id = t.{$colunaRel}");

        if (!empty($dataInicio)) {
            $builder->where("DATE(t.{$colunaData}) >=", date('Y-m-d', strtotime($dataInicio)));
        }

        if (!empty($dataFim)) {
            $builder->where("DATE(t.{$colunaData}) <=", date('Y-m-d', strtotime($dataFim)));
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
        $colunaRel = $this->detectarColunaRelacionamento();
        $colunaData = $this->detectarColunaData();

        $builder = $this->db->table('transacoes t');
        $builder->select("
            SUM(CASE WHEN t.tipo = 'RECEBER' THEN t.valor ELSE 0 END) as total_receber,
            SUM(CASE WHEN t.tipo = 'PAGAR' THEN t.valor ELSE 0 END) as total_pagar,
            SUM(CASE WHEN t.tipo = 'RECEBER' THEN t.valor ELSE -t.valor END) as saldo,
            COUNT(t.id) as quantidade_total
        ");
        $builder->join('pessoas p', "p.id = t.{$colunaRel}");

        if (!empty($dataInicio)) {
            $builder->where("DATE(t.{$colunaData}) >=", date('Y-m-d', strtotime($dataInicio)));
        }

        if (!empty($dataFim)) {
            $builder->where("DATE(t.{$colunaData}) <=", date('Y-m-d', strtotime($dataFim)));
        }

        if (!empty($tipoCliente) && $tipoCliente != 'todos') {
            $builder->where('p.tipo_cliente', $tipoCliente);
        }

        if (!empty($tipoTransacao) && $tipoTransacao != 'todos') {
            $builder->where('t.tipo', $tipoTransacao);
        }

        $result = $builder->get()->getRowArray();

        // Garante valores padrão
        return [
            'total_receber' => $result['total_receber'] ?? 0,
            'total_pagar' => $result['total_pagar'] ?? 0,
            'saldo' => $result['saldo'] ?? 0,
            'quantidade_total' => $result['quantidade_total'] ?? 0
        ];
    }

    /**
     * Método público para obter a coluna de relacionamento (ADICIONADO)
     */
    public function getColunaRelacionamento()
    {
        return $this->detectarColunaRelacionamento();
    }

    /**
     * Método público para definir manualmente a coluna
     */
    public function setColunaRelacionamento($nome)
    {
        $this->colunaRelacionamento = $nome;
        return $this;
    }

    /**
     * Teste de conexão e estrutura
     */
    public function testarConexao()
    {
        $db = \Config\Database::connect();

        $resultados = [
            'conexao' => false,
            'tabela_transacoes' => false,
            'tabela_pessoas' => false,
            'coluna_relacionamento' => null,
            'total_transacoes' => 0,
            'total_pessoas' => 0
        ];

        try {
            $db->connect();
            $resultados['conexao'] = true;

            // Verifica se tabelas existem
            $tabelas = $db->listTables();
            $resultados['tabela_transacoes'] = in_array('transacoes', $tabelas);
            $resultados['tabela_pessoas'] = in_array('pessoas', $tabelas);

            // Conta registros
            if ($resultados['tabela_transacoes']) {
                $query = $db->query("SELECT COUNT(*) as total FROM transacoes");
                $resultados['total_transacoes'] = $query->getRow()->total;
            }

            if ($resultados['tabela_pessoas']) {
                $query = $db->query("SELECT COUNT(*) as total FROM pessoas");
                $resultados['total_pessoas'] = $query->getRow()->total;
            }

            // Detecta coluna de relacionamento
            $resultados['coluna_relacionamento'] = $this->detectarColunaRelacionamento();
        } catch (\Exception $e) {
            $resultados['erro'] = $e->getMessage();
        }

        return $resultados;
    }
}

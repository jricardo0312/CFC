<?php

namespace App\Controllers;

use App\Models\RelatorioModel;

class RelatorioTipoClienteController extends BaseController
{
    public function index()
    {
        $model = new RelatorioModel();

        // Teste rápido - descomente para ver se a consulta básica funciona
        // echo "<pre>";
        // print_r($model->testarConsulta());
        // echo "</pre>";
        // return;

        // Obter parâmetros do filtro
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $tipoCliente = $this->request->getGet('tipo_cliente');
        $tipoTransacao = $this->request->getGet('tipo_transacao');

        // Buscar dados
        $data['transacoes'] = $model->getTransacoesPorTipoCliente($dataInicio, $dataFim, $tipoCliente, $tipoTransacao);
        $data['totais'] = $model->getTotalPorTipoCliente($dataInicio, $dataFim, $tipoCliente, $tipoTransacao);
        $data['tiposCliente'] = $model->getTiposCliente();
        $data['tiposTransacao'] = $model->getTiposTransacao();
        $data['resumoGeral'] = $model->getResumoGeral($dataInicio, $dataFim, $tipoCliente, $tipoTransacao);

        // Contadores para debug
        $data['contagem'] = [
            'transacoes' => count($data['transacoes']),
            'tipos_cliente' => count($data['tiposCliente']),
            'tipos_transacao' => count($data['tiposTransacao'])
        ];

        // Passar filtros para a view
        $data['filtros'] = [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'tipo_cliente' => $tipoCliente,
            'tipo_transacao' => $tipoTransacao
        ];

        return view('relatorio_tipo_cliente', $data);
    }

    public function exportar()
    {
        $model = new RelatorioModel();

        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $tipoCliente = $this->request->getGet('tipo_cliente');
        $tipoTransacao = $this->request->getGet('tipo_transacao');

        $transacoes = $model->getTransacoesPorTipoCliente($dataInicio, $dataFim, $tipoCliente, $tipoTransacao);

        $filename = 'transacoes_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Cabeçalho
        fputcsv($output, ['ID', 'Data Caixa', 'Data Vencimento', 'Cliente', 'Tipo Cliente', 'Tipo Transação', 'Valor', 'Status', 'Descrição'], ';');

        // Dados
        foreach ($transacoes as $row) {
            fputcsv($output, [
                $row['id'],
                $row['data_caixa'],
                $row['data_vencimento'] ?? '',
                $row['nome'] ?? '',
                $row['tipo_cliente'] ?? '',
                $row['tipo'] ?? '',
                number_format($row['valor'], 2, ',', '.'),
                $row['status'] ?? '',
                $row['descricao'] ?? ''
            ], ';');
        }

        fclose($output);
        exit();
    }

    /**
     * Método de teste simples
     */
    public function teste()
    {
        $model = new RelatorioModel();

        echo "<h1>Teste de Consulta</h1>";

        // Teste 1: Consulta básica
        echo "<h2>1. Teste básico (5 registros):</h2>";
        $teste = $model->testarConsulta();
        echo "<pre>";
        print_r($teste);
        echo "</pre>";

        // Teste 2: Tipos de cliente
        echo "<h2>2. Tipos de cliente disponíveis:</h2>";
        $tipos = $model->getTiposCliente();
        echo "<pre>";
        print_r($tipos);
        echo "</pre>";

        // Teste 3: Tipos de transação
        echo "<h2>3. Tipos de transação disponíveis:</h2>";
        $tiposTrans = $model->getTiposTransacao();
        echo "<pre>";
        print_r($tiposTrans);
        echo "</pre>";

        // Teste 4: Consulta com filtros
        echo "<h2>4. Teste com alguns filtros:</h2>";
        $filtrada = $model->getTransacoesPorTipoCliente('2025-01-01', '2025-01-31', null, 'RECEBER');
        echo "Total encontrado: " . count($filtrada) . "<br>";
        if (!empty($filtrada)) {
            echo "<pre>";
            print_r(array_slice($filtrada, 0, 3));
            echo "</pre>";
        }
    }
}

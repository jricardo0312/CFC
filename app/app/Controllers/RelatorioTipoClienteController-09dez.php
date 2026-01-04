<?php

namespace App\Controllers;

use App\Models\RelatorioModel;

class RelatorioTipoClienteController extends BaseController
{
    public function index()
    {
        $model = new RelatorioModel();

        // Obter parâmetros do filtro
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $tipoCliente = $this->request->getGet('tipo_cliente');

        // Buscar dados
        $data['transacoes'] = $model->getTransacoesPorTipoCliente($dataInicio, $dataFim, $tipoCliente);
        $data['totais'] = $model->getTotalPorTipoCliente($dataInicio, $dataFim, $tipoCliente);
        $data['tiposCliente'] = $model->getTiposCliente();

        // Passar filtros para a view
        $data['filtros'] = [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'tipo_cliente' => $tipoCliente
        ];

        return view('relatorio_tipo_cliente', $data);
    }

    public function exportar()
    {
        $model = new RelatorioModel();

        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $tipoCliente = $this->request->getGet('tipo_cliente');

        $transacoes = $model->getTransacoesPorTipoCliente($dataInicio, $dataFim, $tipoCliente);

        $filename = 'transacoes_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Cabeçalho
        fputcsv($output, ['ID', 'Data', 'Cliente', 'Tipo Cliente', 'Valor'], ';');

        // Dados
        foreach ($transacoes as $row) {
            fputcsv($output, [
                $row['id'],
                $row['data_caixa'],
                $row['nome'],
                $row['tipo_cliente'],
                number_format($row['valor'], 2, ',', '.')
            ], ';');
        }

        fclose($output);
        exit();
    }
}

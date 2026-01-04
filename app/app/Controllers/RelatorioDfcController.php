<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Controller;
use App\Models\TransacaoModel;

class RelatorioDfcController extends Controller
{
    /**
     * Define as datas padrão do período de relatório (Mês Atual).
     * @return array [dataInicio, dataFim]
     */
    private function _getDatasPadrao(): array
    {
        $primeiroDia = date('Y-m-01');
        $ultimoDia   = date('Y-m-t');
        return [$primeiroDia, $ultimoDia];
    }

    /**
     * Exibe o Relatório DFC (Demonstração do Fluxo de Caixa).
     */
    public function index()
    {
        $model = new TransacaoModel();
        $request = $this->request;

        // 1. Definição das datas, priorizando a submissão via POST
        [$primeiroDia, $ultimoDia] = $this->_getDatasPadrao();

        $dataInicio = $request->getPost('data_inicio') ?? $primeiroDia;
        $dataFim    = $request->getPost('data_fim') ?? $ultimoDia;

        // --- 2. BUSCA DO SALDO ANTERIOR ---
        // Aqui está a chave: chamamos o método que foi corrigido no Model.
        $saldoInicial = $model->getSaldoInicial($dataInicio);

        // --- 3. BUSCA DOS RESULTADOS DO DFC ---
        $dfc_resultados = $model->gerarRelatorioDFC($dataInicio, $dataFim);

        // INSERINDO O SALDO ANTERIOR NO ARRAY DE RESULTADOS DO DFC
        // A chave 'SALDO_ANTERIOR' deve ser exatamente a esperada pela View (relatorio_dfc.php)
        $dfc_resultados['SALDO_ANTERIOR'] = $saldoInicial;

        // --- 4. PREPARAÇÃO DA VIEW ---
        $data = [
            'titulo'         => 'Relatório DFC - Fluxo de Caixa',
            'data_inicio'    => $dataInicio,
            'data_fim'       => $dataFim,
            'dfc_resultados' => $dfc_resultados,
        ];

        return view('financeiro/relatorio_dfc', $data);
    }
}

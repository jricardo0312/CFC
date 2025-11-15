<?php

namespace App\Controllers;

use App\Models\TransacaoModel;
use App\Models\CategoriasFinanceirasModel;
use App\Models\ClientesFornecedoresModel;
use CodeIgniter\Controller;

// ESTE CÓDIGO É PARTE DO FinanceiroController.php COMPLETO.

class FinanceiroController extends Controller
{
    protected $transacaoModel;
    protected $categoriaModel;
    protected $pessoaModel;

    public function __construct()
    {
        $this->transacaoModel = new TransacaoModel();
        $this->categoriaModel = new CategoriasFinanceirasModel();
        $this->pessoaModel = new ClientesFornecedoresModel();
        helper(['form', 'url']);
    }

    // Helper para adicionar nomes de Categoria e Pessoa às transações.
    private function _enrichTransacoes(array $transacoes): array
    {
        $categorias = $this->categoriaModel->findAll();
        $pessoas = $this->pessoaModel->findAll();
        $categoriasMap = array_column($categorias, 'nome', 'id');
        $pessoasMap = array_column($pessoas, 'nome', 'id');

        foreach ($transacoes as &$transacao) {
            $transacao['categoria_nome'] = $categoriasMap[$transacao['categoria_id']] ?? 'N/A';
            $transacao['pessoa_nome'] = $pessoasMap[$transacao['pessoa_id']] ?? 'N/A';
        }
        return $transacoes;
    }

    // Exibe o Dashboard: Listagem de Contas Pendentes (o que o DFC ainda não viu).
    public function index()
    {
        $data = [
            'contas_a_receber' => $this->transacaoModel
                ->where('tipo', 'RECEBER')->where('status', 'PENDENTE')->findAll(),
            'contas_a_pagar' => $this->transacaoModel
                ->where('tipo', 'PAGAR')->where('status', 'PENDENTE')->findAll(),
            'title' => 'Dashboard Financeiro - Contas Pendentes',
        ];

        $data['contas_a_receber'] = $this->_enrichTransacoes($data['contas_a_receber']);
        $data['contas_a_pagar'] = $this->_enrichTransacoes($data['contas_a_pagar']);

        echo view('templates/header', $data);
        echo view('financeiro/dashboard', $data);
        echo view('templates/footer');
    }

    // LIQUIDAÇÃO DE CAIXA: Atualiza o status para CONCLUIDA e registra a data_caixa.
    public function liquidarCaixa($id)
    {
        $dataCaixa = $this->request->getPost('data_caixa_real');
        if (empty($dataCaixa)) {
            return redirect()->to('/financeiro')->with('error', 'A data de caixa é obrigatória para liquidar a transação.');
        }

        $this->transacaoModel->update($id, [
            'status' => 'CONCLUIDA',
            'data_caixa' => $dataCaixa, // Data efetiva do fluxo de caixa
        ]);

        return redirect()->to('/financeiro')->with('success', 'Transação liquidada e contabilizada no Fluxo de Caixa (DFC).');
    }

    // ESTE CÓDIGO É A CONTINUAÇÃO DO FinanceiroController.php COMPLETO.

    // Exibe o formulário de criação de nova transação.
    public function novaTransacao()
    {
        $data = [
            'categorias' => $this->categoriaModel->findAll(),
            'pessoas' => $this->pessoaModel->findAll(),
            'title' => 'Nova Transação',
        ];

        echo view('templates/header', $data);
        echo view('financeiro/formulario_transacao', $data);
        echo view('templates/footer');
    }

    // Salva a nova transação no banco de dados.
    public function salvarTransacao()
    {
        $rules = [
            'tipo' => 'required|in_list[PAGAR,RECEBER]',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|valid_date',
            'categoria_id' => 'required|is_natural_no_zero',
            'pessoa_id' => 'required|is_natural_no_zero',
            'descricao' => 'required',
        ];

        if (!$this->validate($rules)) {
            // Lógica de retorno com erros de validação
            $data = [
                'validation' => $this->validator,
                'categorias' => $this->categoriaModel->findAll(),
                'pessoas' => $this->pessoaModel->findAll(),
                'title' => 'Nova Transação - Erro',
            ];
            return view('templates/header', $data)
                . view('financeiro/formulario_transacao', $data)
                . view('templates/footer');
        }

        $this->transacaoModel->save([
            'tipo' => $this->request->getPost('tipo'),
            'valor' => $this->request->getPost('valor'),
            'data_vencimento' => $this->request->getPost('data_vencimento'),
            'descricao' => $this->request->getPost('descricao'),
            'categoria_id' => $this->request->getPost('categoria_id'),
            'pessoa_id' => $this->request->getPost('pessoa_id'),
            'status' => 'PENDENTE', // Inicia sempre como PENDENTE
        ]);

        return redirect()->to('/financeiro')->with('success', 'Transação cadastrada com sucesso! Pendente de liquidação (caixa).');
    }

    // ESTE CÓDIGO É A CONTINUAÇÃO E FINAL DO FinanceiroController.php COMPLETO.

    // MÓDULO DE RELATÓRIO DFC: Exibe o formulário de filtro e, se houver filtro, o relatório.
    public function relatorioDFC()
    {
        $data = [
            'title' => 'Demonstração dos Fluxos de Caixa (DFC) - Método Direto',
            'dfc_resultados' => null,
            // Valores padrão para o filtro: início e fim do mês atual
            'data_inicio' => $this->request->getPost('data_inicio') ?? date('Y-m-01'),
            'data_fim' => $this->request->getPost('data_fim') ?? date('Y-m-t'),
            'error' => null,
        ];

        // O '|| true' garante que o relatório sempre seja gerado com as datas padrão na primeira carga
        if ($this->request->getPost() || true) {
            $dataInicio = $data['data_inicio'];
            $dataFim = $data['data_fim'];

            if (strtotime($dataInicio) > strtotime($dataFim)) {
                $data['error'] = 'A Data Inicial não pode ser maior que a Data Final.';
            } else {
                // 1. Calcular FCO (Fluxo de Caixa Operacional)
                // O método getFluxoLiquidoPorTipo deve ser implementado no TransacaoModel.php
                $fco = $this->transacaoModel->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCO');

                // 2. Calcular FCI (Fluxo de Caixa de Investimento)
                $fci = $this->transacaoModel->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCI');

                // 3. Calcular FCF (Fluxo de Caixa de Financiamento)
                $fcf = $this->transacaoModel->getFluxoLiquidoPorTipo($dataInicio, $dataFim, 'FCF');

                // 4. Consolidar Resultados
                $fco_liquido = $fco['liquido'] ?? 0;
                $fci_liquido = $fci['liquido'] ?? 0;
                $fcf_liquido = $fcf['liquido'] ?? 0;

                $data['dfc_resultados'] = [
                    'fco' => [
                        'entradas' => $fco['entradas'] ?? 0,
                        'saidas' => $fco['saidas'] ?? 0,
                        'liquido' => $fco_liquido,
                    ],
                    'fci' => [
                        'entradas' => $fci['entradas'] ?? 0,
                        'saidas' => $fci['saidas'] ?? 0,
                        'liquido' => $fci_liquido,
                    ],
                    'fcf' => [
                        'entradas' => $fcf['entradas'] ?? 0,
                        'saidas' => $fcf['saidas'] ?? 0,
                        'liquido' => $fcf_liquido,
                    ],
                    'total_liquido' => $fco_liquido + $fci_liquido + $fcf_liquido,
                ];
            }
        }

        echo view('templates/header', $data);
        echo view('financeiro/relatorio_dfc', $data);
        echo view('templates/footer');
    }
}

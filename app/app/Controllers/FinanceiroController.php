<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\TransacaoModel;
use App\Models\PessoaModel;
use App\Models\CategoriasFinanceirasModel;

class FinanceiroController extends BaseController
{
    // Adicione esta propriedade para carregar o model
    // protected $transacaoModel;

    /**
     * @var TransacaoModel
     */
    private $transacaoModel;

    /**
     * @var PessoaModel
     */
    private $pessoaModel;

    /**
     * @var CategoriasFinanceirasModel
     */
    private $categoriaModel;


    /**
     * Construtor para injetar os Models necessários.
     */
    public function __construct()
    {
        $this->transacaoModel = new TransacaoModel();
        $this->pessoaModel = new PessoaModel();
        $this->categoriaModel = new CategoriasFinanceirasModel();

        // Helpers (se ainda não estiverem no BaseController)
        helper(['form', 'url']);
    }

    // --------------------------------------------------------------------
    // MÓDULO: CONTAS PENDENTES (Index Principal do Financeiro)
    // --------------------------------------------------------------------

    /**
     * Lista todas as transações PENDENTES (Contas a Pagar e Receber).
     * Rota: /financeiro
     */
    public function index()
    {
        $data = [
            'titulo' => 'Contas Pendentes (Regime de Competência)',
            // Buscamos apenas transações com status PENDENTE
            'transacoes' => $this->transacaoModel->where('status', 'PENDENTE')->findAll(),
        ];

        return view('transacoes/index', $data);
    }


    // --------------------------------------------------------------------
    // MÓDULO: CADASTRO DE TRANSAÇÕES
    // --------------------------------------------------------------------

    /**
     * Exibe o formulário de cadastro de nova transação.
     * Rota: /financeiro/novo
     */
    public function novaTransacao()
    {
        $data = [
            'titulo'     => 'Nova Transação (A Pagar ou A Receber)',
            'pessoas'    => $this->pessoaModel->findAll(),
            'categorias' => $this->categoriaModel->findAll(),
        ];

        return view('transacoes/formulario', $data);
    }

    /**
     * Salva ou atualiza uma transação.
     * Rota: /financeiro/salvar (POST)
     */
    public function salvarTransacao()
    {
        $post = $this->request->getPost();

        $post['status'] = 'PENDENTE';
        unset($post['data_caixa']);

        if (isset($post['valor'])) {
            $post['valor'] = str_replace(',', '.', $post['valor']);
        }

        if (!$this->transacaoModel->save($post)) {
            return redirect()->back()
                ->with('erros', $this->transacaoModel->errors())
                ->with('erro', 'Verifique os erros de validação abaixo.')
                ->withInput();
        }

        $mensagem = (isset($post['id']) && $post['id'] > 0) ? 'Transação atualizada com sucesso!' : 'Transação registrada com sucesso como PENDENTE!';

        return redirect()->to(route_to('financeiro_index'))
            ->with('sucesso', $mensagem);
    }

    // --------------------------------------------------------------------
    // MÓDULO: LIQUIDAÇÃO (Dar Baixa no Caixa)
    // --------------------------------------------------------------------

    /**
     * Dá baixa em uma transação pendente, movendo-a para o caixa (status CONCLUIDA).
     * Rota: /financeiro/liquidar/(:num) (POST)
     * @param int $id ID da transação
     */
    public function liquidarCaixa(int $id)
    {
        $transacao = $this->transacaoModel->find($id);

        if (!$transacao) {
            return redirect()->back()->with('erro', 'Transação não encontrada.');
        }

        if ($transacao['status'] == 'CONCLUIDA') {
            return redirect()->back()->with('erro', 'Esta transação já está concluída.');
        }

        $dadosLiquidacao = [
            'id' => $id,
            'status' => 'CONCLUIDA',
            // A data de caixa é a data da liquidação
            'data_caixa' => date('Y-m-d H:i:s'),
        ];

        if ($this->transacaoModel->save($dadosLiquidacao)) {
            return redirect()->to(route_to('financeiro_index'))
                ->with('sucesso', 'Transação liquidada e contabilizada no Fluxo de Caixa!');
        } else {
            return redirect()->back()
                ->with('erro', 'Não foi possível liquidar a transação.');
        }
    }

    /**
     * Exibe o formulário de filtro e o resultado do relatório DFC.
     * Rota: /financeiro/dfc (GET ou POST)
     */
    public function relatorioDFC()
    {
        $data = [
            'titulo' => 'Relatório DFC (Demonstração dos Fluxos de Caixa)',
            'dfc_resultados' => null,
            'data_inicio' => $this->request->getPost('data_inicio') ?? date('Y-m-01'),
            'data_fim' => $this->request->getPost('data_fim') ?? date('Y-m-t'),
        ];

        // Usar is('post') é a forma mais robusta no CI4
        if ($this->request->is('post')) {

            $dataInicio = $this->request->getPost('data_inicio');
            $dataFim    = $this->request->getPost('data_fim');

            // Validação simples de datas
            if (!empty($dataInicio) && !empty($dataFim) && strtotime($dataFim) >= strtotime($dataInicio)) {

                // 1. Busca os resultados do DFC para o período (FCO, FCI, FCF)
                $resultados = $this->transacaoModel->gerarRelatorioDFC($dataInicio, $dataFim);

                // --- INÍCIO DA LÓGICA DE SALDO ANTERIOR ---

                // 2. Calcula o saldo acumulado de TUDO antes da data de início.
                // Usamos data_caixa, pois é um relatório de CAIXA (liquidado)
                $saldoAnterior = $this->transacaoModel
                    ->select("SUM(CASE WHEN tipo = 'ENTRADA' THEN valor ELSE -valor END) as saldo_acumulado")
                    ->where('status', 'CONCLUIDA')
                    ->where('data_caixa <', $dataInicio) // Importante: Apenas o que foi liquidado ANTES
                    ->get()
                    ->getRow()
                    ->saldo_acumulado ?? 0;

                // 3. Adiciona o saldo anterior ao array de resultados
                // Primeiro, garante que $resultados é um array
                if (!is_array($resultados)) {
                    $resultados = [];
                }

                $resultados['SALDO_ANTERIOR'] = (float) $saldoAnterior;

                // --- FIM DA LÓGICA DE SALDO ANTERIOR ---


                // Lógica de verificação e mensagens (mantida)
                if (!empty($resultados) && isset($resultados['TOTAL'])) {

                    $data['dfc_resultados'] = $resultados;

                    // Se não houve movimento NO PERÍODO e também não havia NADA antes.
                    if ($resultados['TOTAL'] == 0 && $saldoAnterior == 0) {
                        session()->setFlashdata('erro', 'Nenhuma movimentação de caixa (liquidada) foi encontrada no período ou antes dele.');
                    }
                    // Se não houve movimento no período, mas temos saldo anterior
                    else if ($resultados['TOTAL'] == 0 && $saldoAnterior != 0) {
                        session()->setFlashdata('sucesso', 'Nenhuma movimentação de caixa encontrada no período. O saldo final reflete apenas o saldo anterior.');
                    }
                } else {
                    // Se o Model retornou vazio (null, [], false), definimos a mensagem de erro.
                    session()->setFlashdata('erro', 'Nenhuma transação liquidada (movimentação de caixa) foi encontrada no período selecionado.');
                }
            } else {
                session()->setFlashdata('erro', 'Selecione um período de datas válido (Data Fim deve ser igual ou posterior à Data Início).');
            }
        }

        // A view 'financeiro/relatorio_dfc' agora receberá $dfc_resultados['SALDO_ANTERIOR']
        return view('financeiro/relatorio_dfc', $data);
    }
}

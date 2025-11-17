<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\TransacaoModel;
use App\Models\PessoaModel;
use App\Models\CategoriasFinanceirasModel;

class FinanceiroController extends Controller
{
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


    // --------------------------------------------------------------------
    // MÓDULO: RELATÓRIO DFC (Demonstração dos Fluxos de Caixa)
    // --------------------------------------------------------------------

    /**
     * Exibe o formulário de filtro e o resultado do relatório DFC.
     * Rota: /financeiro/dfc (GET ou POST)
     */
    public function relatorioDFC()
    {
        // // VAMOS TESTAR O MÉTODO DA REQUISIÇÃO BEM AQUI
        // dd($this->request->getMethod());

        $data = [
            'titulo' => 'Relatório DFC (Demonstração dos Fluxos de Caixa)',
            'dfc_resultados' => null,
            'data_inicio' => $this->request->getPost('data_inicio') ?? date('Y-m-01'),
            'data_fim' => $this->request->getPost('data_fim') ?? date('Y-m-t'),
        ];

        // Se o formulário foi submetido (POST)
        if ($this->request->getMethod() === 'POST') {

            $dataInicio = $this->request->getPost('data_inicio');
            $dataFim    = $this->request->getPost('data_fim');

            // Validação simples de datas
            if (!empty($dataInicio) && !empty($dataFim) && strtotime($dataFim) >= strtotime($dataInicio)) {

                // Chamada ao método do Model
                $resultados = $this->transacaoModel->gerarRelatorioDFC($dataInicio, $dataFim);

                dd($resultados);

                // --- LÓGICA DE VERIFICAÇÃO CORRIGIDA ---
                // PRIMEIRO, verificamos se o Model retornou algo que não seja vazio (null, [], false)
                // E se é um array (como esperado).
                if (!empty($resultados) && is_array($resultados)) {
                    // SÓ ENTÃO passamos os resultados para a View
                    $data['dfc_resultados'] = $resultados;

                    // AGORA, podemos checar com segurança a chave 'TOTAL' (se ela existir)
                    // A sua view não parece usar o TOTAL, mas mantive a lógica para a mensagem de erro.
                    if (isset($resultados['TOTAL']) && $resultados['TOTAL'] == 0) {
                        session()->setFlashdata('erro', 'Nenhuma transação liquidada (movimentação de caixa) foi encontrada no período selecionado.');
                    }
                } else {
                    // Se o Model retornou vazio (null, [], false), definimos a mensagem de erro.
                    session()->setFlashdata('erro', 'Nenhuma transação liquidada (movimentação de caixa) foi encontrada no período selecionado.');
                    // $data['dfc_resultados'] continua 'null', o que fará a view mostrar o 'else' (corretamente).
                }
            } else {
                session()->setFlashdata('erro', 'Selecione um período de datas válido (Data Fim deve ser igual ou posterior à Data Início).');
            }
        }

        return view('financeiro/relatorio_dfc', $data);
    }
}

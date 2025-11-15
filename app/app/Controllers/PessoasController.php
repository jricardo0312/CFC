<?php

namespace App\Controllers;

use App\Models\PessoaModel;
use CodeIgniter\Controller;

class PessoasController extends Controller
{
    // Carrega o Form Helper e o URL Helper
    protected $helpers = ['form', 'url'];

    /**
     * Rota: /pessoas
     * Lista todas as pessoas
     */
    public function index()
    {
        $model = new PessoaModel();

        $data = [
            'titulo' => 'Listagem de Pessoas Cadastradas',
            'pessoas' => $model->findAll(),
        ];

        return view('pessoas/index', $data);
    }

    /**
     * Rota: /pessoas/nova
     * Exibe o formulário (renomeado) para um novo cadastro
     */
    public function nova()
    {
        $data = [
            'titulo' => 'Cadastrar Nova Pessoa',
        ];

        // Usa a nova view 'formulario'
        return view('pessoas/formulario_update', $data);
    }

    /**
     * Rota: /pessoas/editar/(:num)
     * Exibe o formulário (renomeado) preenchido para edição
     */
    public function editar($id = null)
    {
        $model = new PessoaModel();
        $pessoa = $model->find($id);

        if (!$pessoa) {
            session()->setFlashdata('erro', 'Pessoa não encontrada.');
            return redirect()->to(route_to('pessoas_index'));
        }

        $data = [
            'titulo' => 'Editar Pessoa: ' . esc($pessoa['nome']),
            'pessoa' => $pessoa, // Passa os dados da pessoa para a view
        ];

        // Reutiliza a view 'formulario'
        return view('pessoas/formulario_update', $data);
    }

    /**
     * Rota: POST /pessoas/salvar
     * Processa tanto o CADASTRO quanto a ATUALIZAÇÃO
     */
    public function salvar()
    {
        $model = new PessoaModel();

        // Pega todos os dados do POST (incluindo o 'id' se for edição)
        $data = $this->request->getPost();

        // O Model->validate($data) agora funciona para update
        // porque o $data contém o 'id' e o Model usa {id} nas regras
        if (!$model->validate($data)) {

            // 1. FALHA NA VALIDAÇÃO:
            session()->setFlashdata('erros', $model->errors());
            return redirect()->back()->withInput();
        }

        // 2. VALIDAÇÃO BEM-SUCEDIDA:
        // O Model->save() é inteligente:
        // Se $data['id'] existe, ele faz UPDATE.
        // Se $data['id'] não existe, ele faz INSERT.
        if ($model->save($data)) {
            session()->setFlashdata('sucesso', 'Pessoa salva com sucesso!');
        } else {
            session()->setFlashdata('erro', 'Erro interno ao salvar a pessoa.');
        }

        return redirect()->to(route_to('pessoas_index'));
    }

    /**
     * Rota: DELETE /pessoas/excluir/(:num)
     * Processa a exclusão
     */
    public function excluir($id = null)
    {
        $model = new PessoaModel();
        $pessoa = $model->find($id);

        if ($pessoa) {
            if ($model->delete($id)) {
                session()->setFlashdata('sucesso', 'Pessoa excluída com sucesso!');
            } else {
                session()->setFlashdata('erro', 'Não foi possível excluir a pessoa.');
            }
        } else {
            session()->setFlashdata('erro', 'Pessoa não encontrada para exclusão.');
        }

        return redirect()->to(route_to('pessoas_index'));
    }
}

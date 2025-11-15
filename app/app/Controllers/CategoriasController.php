<?php

namespace App\Controllers;

use App\Models\CategoriasFinanceirasModel; // Usaremos este Model
use CodeIgniter\Controller;

class CategoriasController extends Controller
{
    // Carrega o Form Helper e o URL Helper
    protected $helpers = ['form', 'url'];

    /**
     * Rota: /categorias
     * Lista todas as categorias financeiras
     */
    public function index()
    {
        $model = new CategoriasFinanceirasModel();

        $data = [
            'titulo' => 'Categorias Financeiras (Plano de Contas DFC)',
            'categorias' => $model->findAll(),
        ];

        return view('Categorias/index', $data);
    }

    /**
     * Rota: /categorias/nova
     * Exibe o formulário para um novo cadastro
     */
    public function nova()
    {
        $data = [
            'titulo' => 'Nova Categoria Financeira',
        ];
        return view('Categorias/formulario', $data);
    }

    /**
     * Rota: /categorias/editar/(:num)
     * Exibe o formulário preenchido para edição
     */
    public function editar($id = null)
    {
        $model = new CategoriasFinanceirasModel();
        $categoria = $model->find($id);

        if (!$categoria) {
            session()->setFlashdata('erro', 'Categoria não encontrada.');
            return redirect()->to(route_to('categorias_index'));
        }

        $data = [
            'titulo' => 'Editar Categoria: ' . esc($categoria['nome']),
            'categoria' => $categoria, // Passa os dados para a view
        ];

        return view('Categorias/formulario', $data);
    }

    /**
     * Rota: POST /categorias/salvar
     * Processa tanto o CADASTRO quanto a ATUALIZAÇÃO
     */
    public function salvar()
    {
        $model = new CategoriasFinanceirasModel();
        $data = $this->request->getPost();

        // Tenta validar os dados (o Model deve ter as $validationRules)
        if (!$model->validate($data)) {
            session()->setFlashdata('erros', $model->errors());
            return redirect()->back()->withInput();
        }

        // O Model->save() faz o INSERT ou UPDATE
        if ($model->save($data)) {
            session()->setFlashdata('sucesso', 'Categoria salva com sucesso!');
        } else {
            session()->setFlashdata('erro', 'Erro interno ao salvar a categoria.');
        }

        return redirect()->to(route_to('categorias_index'));
    }

    /**
     * Rota: DELETE /categorias/excluir/(:num)
     * Processa a exclusão
     */
    public function excluir($id = null)
    {
        $model = new CategoriasFinanceirasModel();
        $categoria = $model->find($id);

        if ($categoria) {
            // NOTA: Em um sistema real, verificaríamos se esta categoria
            // não está sendo usada por nenhuma transação antes de excluir.

            if ($model->delete($id)) {
                session()->setFlashdata('sucesso', 'Categoria excluída com sucesso!');
            } else {
                session()->setFlashdata('erro', 'Não foi possível excluir a categoria.');
            }
        } else {
            session()->setFlashdata('erro', 'Categoria não encontrada para exclusão.');
        }

        return redirect()->to(route_to('categorias_index'));
    }
}

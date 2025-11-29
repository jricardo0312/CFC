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
     * Lista todas as pessoas (COM ORDENAÇÃO)
     */
    // public function index()
    // {
    //     $model = new PessoaModel();

    //     // 1. Captura os parâmetros de ordenação da URL (GET)
    //     // Se não existirem, define o padrão: ordenar por 'id' de forma 'asc' (crescente)
    //     $ordem   = $this->request->getGet('ordem') ?? 'id';
    //     $direcao = $this->request->getGet('direcao') ?? 'asc';

    //     // 2. Validação de Segurança (Whitelist)
    //     // Isso impede que alguém digite "?ordem=senha" ou injeção de SQL na URL
    //     $colunasPermitidas = ['id', 'nome', 'email', 'documento'];

    //     if (!in_array($ordem, $colunasPermitidas)) {
    //         $ordem = 'id'; // Se a coluna for inválida, volta para o padrão
    //     }

    //     // Garante que a direção seja apenas 'asc' ou 'desc'
    //     $direcao = (strtolower($direcao) === 'desc') ? 'desc' : 'asc';

    //     // 3. Busca os dados ordenados
    //     $data = [
    //         'titulo'  => 'Listagem de Pessoas Cadastradas',
    //         // Aplica o orderBy antes do findAll
    //         'pessoas' => $model->orderBy($ordem, $direcao)->findAll(),

    //         // 4. Passa as variáveis de controle para a View (para as setinhas funcionarem)
    //         'ordem'   => $ordem,
    //         'direcao' => $direcao,
    //     ];

    //     return view('pessoas/index', $data);
    // }


    public function index()
    {
        $model = new PessoaModel();

        // 1. Captura ordenação
        $ordem   = $this->request->getGet('ordem') ?? 'id';
        $direcao = $this->request->getGet('direcao') ?? 'asc';

        // 2. Whitelist de segurança
        $colunasPermitidas = ['id', 'nome', 'email', 'documento'];
        if (!in_array($ordem, $colunasPermitidas)) {
            $ordem = 'id';
        }
        $direcao = (strtolower($direcao) === 'desc') ? 'desc' : 'asc';

        // 3. Busca os dados COM PAGINAÇÃO
        // O método paginate() substitui o findAll() e já considera o limite por página
        $pessoas = $model->orderBy($ordem, $direcao)->paginate(10);

        $data = [
            'titulo'  => 'Listagem de Pessoas',
            'pessoas' => $pessoas,
            'pager'   => $model->pager, // Objeto necessário para gerar os links (1, 2, 3...)
            'ordem'   => $ordem,
            'direcao' => $direcao,
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

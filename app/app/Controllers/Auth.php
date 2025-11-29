<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    /**
     * Mostra o formulário de login.
     * @return string
     */
    public function login()
    {
        // Carrega a view 'login' com o layout e o formulário.
        return view('login');
    }

    /**
     * Processa o envio do formulário de login.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function tentarLogin()
    {
        // 1. Obtém os dados de email e senha do formulário via requisição POST.
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        // 2. Carrega o Model para interagir com a tabela de usuários.
        $model = new UsuarioModel();

        // 3. Busca o usuário no banco de dados pelo email fornecido.
        $usuario = $model->where('email', $email)->first();

        // 4. Verifica se o usuário existe E se a senha fornecida corresponde ao hash armazenado.
        if ($usuario && password_verify($senha, $usuario['senha'])) {

            // 5. Autenticação bem-sucedida: Cria a sessão do usuário.
            $session = session();
            $session->set([
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'logado' => true,
            ]);

            // 6. Redireciona o usuário para a página protegida (e.g., Dashboard).
            // return redirect()->to('/dashboard')->with('success', 'Login realizado com sucesso!');
            return redirect()->to(base_url('painel'));
            // OU use o alias da rota, que é mais seguro:
            // return redirect()->to(url_to('dashboard'));
        } else {
            // 7. Falha na autenticação: Redireciona de volta para o login com uma mensagem de erro.
            return redirect()->back()->withInput()->with('error', 'Email ou senha incorretos.');
        }
    }

    /**
     * Encerra a sessão do usuário.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function logout()
    {
        // 1. Destrói todos os dados da sessão (desloga o usuário).
        session()->destroy();

        // 2. Redireciona para a página de login com uma mensagem.
        return redirect()->to('/login')->with('success', 'Você foi desconectado.');
    }

    /**
     * Exibe o formulário de cadastro (Opcional, mas útil).
     * @return string
     */
    public function register()
    {
        return view('register');
    }

    /**
     * Processa o envio do formulário de cadastro.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function createAccount()
    {
        // 1. Define as regras de validação para os campos do formulário.
        $rules = [
            'nome' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[usuarios.email]', // Verifica se o email é válido e único na tabela.
            'senha' => 'required|min_length[8]',
            'confirma_senha' => 'required|matches[senha]', // Garante que as senhas sejam iguais.
        ];

        // 2. Tenta validar os dados recebidos.
        if (! $this->validate($rules)) {
            // Se a validação falhar, volta para o formulário mostrando os erros.
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 3. Validação bem-sucedida: Prepara os dados.
        $data = [
            'nome' => $this->request->getPost('nome'),
            'email' => $this->request->getPost('email'),
            'senha' => $this->request->getPost('senha'), // O Model irá hashear essa senha automaticamente!
        ];

        // 4. Salva o novo usuário no banco de dados.
        $model = new UsuarioModel();
        $model->save($data);

        // 5. Redireciona para o login.
        return redirect()->to('/login')->with('success', 'Conta criada com sucesso! Faça login para continuar.');
    }
}

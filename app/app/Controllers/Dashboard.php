<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    /**
     * Define quais filtros (Middleware) serão aplicados a este Controller.
     * * Aqui, aplicamos o filtro 'auth' (AuthFilter) a TODOS os métodos
     * (o asterisco '*') deste Controller. Isso garante que antes de qualquer
     * método ser executado, a sessão do usuário seja checada.
     */
    protected $filters = ['auth'];

    /**
     * Método principal (index) que carrega a página após o login.
     * * @return string
     */
    public function index()
    {
        // 1. O Filtro 'auth' já garantiu que o usuário está logado.

        // 2. Carrega a view 'dashboard.php', que exibe a mensagem de boas-vindas
        //    usando os dados da sessão (ex: session()->get('nome')).
        return view('dashboard');
    }
}

<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Lógica executada ANTES da requisição ser processada pelo Controller.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verifica se o usuário NÃO está logado.
        if (!session()->get('logado')) {
            // Se não estiver logado, redireciona para a página de login.
            return redirect()->to('/login')->with('error', 'Você precisa estar logado para acessar esta página.');
        }
    }

    /**
     * Lógica executada DEPOIS da resposta ser enviada (não usado para autenticação).
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a fazer
    }
}

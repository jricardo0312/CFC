<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class PainelController extends Controller
{
    /**
     * Exibe o Dashboard principal com links para todos os módulos.
     * Rota: / (Página inicial)
     */
    public function index()
    {
        $data = [
            'titulo' => 'Dashboard Financeiro',
            'subtitulo' => 'Controle de Fluxo de Caixa',
        ];

        // Agora chama a view em 'dashboard/index'
        return view('dashboard/index', $data);
    }
}

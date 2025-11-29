<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Auth;

// Use a new instance of the RouteCollection class.
$routes = Services::routes();

// --- Configurações Padrão (Manter Inalterado) ---
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Definições de Rotas Principais
 * --------------------------------------------------------------------
 */

// Rotas de Autenticação
$routes->get('login', [Auth::class, 'login'], ['as' => 'login']);
$routes->post('tentarLogin', [Auth::class, 'tentarLogin'], ['as' => 'tentarLogin']);
$routes->get('logout', [Auth::class, 'logout'], ['as' => 'logout']);

// Rotas de Cadastro (Opcional)
$routes->get('register', [Auth::class, 'register'], ['as' => 'register']);
$routes->post('createAccount', [Auth::class, 'createAccount'], ['as' => 'createAccount']);

// Rota Protegida (Dashboard)
// $routes->get('dashboard', 'Dashboard::index', ['as' => 'dashboard']);


// 1. ROTA PADRÃO (DASHBOARD)
// Mapeia a rota base (/) para o PainelController
// $routes->get('/', 'PainelController::index', ['as' => 'dashboard']);
$routes->get('painel', 'PainelController::index', ['as' => 'dashboard']);

$routes->get('/', 'Auth::login');

// 2. MÓDULO DE PESSOAS (CRUD Completo)
$routes->group('pessoas', function ($routes) {
    $routes->get('/', 'PessoasController::index', ['as' => 'pessoas_index']);
    $routes->get('nova', 'PessoasController::nova', ['as' => 'pessoas_nova']);
    $routes->get('editar/(:num)', 'PessoasController::editar/$1', ['as' => 'pessoas_editar']);
    $routes->post('salvar', 'PessoasController::salvar', ['as' => 'pessoas_salvar']);
    $routes->delete('excluir/(:num)', 'PessoasController::excluir/$1', ['as' => 'pessoas_excluir']);
});

// 3. MÓDULO DE CATEGORIAS (CRUD Completo)
$routes->group('categorias', function ($routes) {
    $routes->get('/', 'CategoriasController::index', ['as' => 'categorias_index']);
    $routes->get('nova', 'CategoriasController::nova', ['as' => 'categorias_nova']);
    $routes->get('editar/(:num)', 'CategoriasController::editar/$1', ['as' => 'categorias_editar']);
    $routes->post('salvar', 'CategoriasController::salvar', ['as' => 'categorias_salvar']);
    $routes->delete('excluir/(:num)', 'CategoriasController::excluir/$1', ['as' => 'categorias_excluir']);
});

// 4. MÓDULO FINANCEIRO (Transações e DFC)
$routes->group('financeiro', function ($routes) {
    // Dashboard de Contas Pendentes (Main Index do Financeiro)
    $routes->get('/', 'FinanceiroController::index', ['as' => 'financeiro_index']);

    // Cadastro de Transação
    $routes->get('novo', 'FinanceiroController::novaTransacao', ['as' => 'nova_transacao']);
    $routes->post('salvar', 'FinanceiroController::salvarTransacao', ['as' => 'salvar_transacao']);

    // Liquidação de Caixa (Movimentação para CONCLUIDA)
    $routes->post('liquidar/(:num)', 'FinanceiroController::liquidarCaixa/$1', ['as' => 'liquidar_caixa']);

    // Relatório DFC
    $routes->match(['get', 'post'], 'financeiro/dfc', 'FinanceiroController::relatorioDFC', ['as' => 'relatorio_dfc']);
    // $routes->match(['get', 'post'], 'relatorio_dfc', 'FinanceiroController::relatorioDFC', ['as' => 'relatorio_dfc']);
    # $routes->match(['get', 'post'], 'relatorio_dfc', 'FinanceiroController::relatorioDFC', ['as' => 'relatorio_dfc']);
});

// RELATORIO DE TRANSAÇÕES
$routes->get('relatorio', 'RelatorioTransacaoController::index', ['as' => 'relatorio_index']);
$routes->get('relatorio/exportarCsv', 'RelatorioTransacaoController::exportarCsv');
$routes->get('teste', function () {
    echo 'Rota funcionando!';
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

<?php
// Este arquivo é o esqueleto HTML que todas as views estendem.
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Usa a seção 'titulo' definida pela view, ou um fallback -->
    <title><?= $this->renderSection('titulo') ?? 'Sistema DFC' ?> | Sistema Financeiro</title>

    <!-- Importação do Tailwind CSS CDN (Necessário para a estética) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- HEADER: Navegação Principal -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4 h-16">
                <div class="flex-shrink-0">
                    <!-- Link para o Dashboard (Rota 'dashboard') -->
                    <a href="<?= route_to('dashboard') ?>" class="text-2xl font-extrabold text-indigo-600 hover:text-indigo-800 transition duration-150">
                        SALUTEM Terapias Integradas LTDA
                    </a>
                </div>
                <!-- Navegação Principal com Rotas -->
                <nav class="hidden sm:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="<?= route_to('dashboard') ?>" class="text-gray-900 hover:bg-indigo-50 hover:text-indigo-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Painel</a>
                        <a href="<?= route_to('financeiro_index') ?>" class="text-gray-500 hover:bg-indigo-50 hover:text-indigo-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Transações</a>
                        <a href="<?= route_to('pessoas_index') ?>" class="text-gray-500 hover:bg-indigo-50 hover:text-indigo-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Pessoas</a>
                        <a href="<?= route_to('categorias_index') ?>" class="text-gray-500 hover:bg-indigo-50 hover:text-indigo-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Categorias</a>
                        <a href="<?= route_to('relatorio_dfc') ?>" class="text-gray-500 hover:bg-indigo-50 hover:text-indigo-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Relatório DFC</a>
                        <a href="<?= route_to('relatorio_index') ?>" class="text-gray-500 hover:bg-indigo-50 hover:text-indigo-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Relatório Transações</a>

                        <a href="<?= url_to('Auth::logout') ?>"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Sair
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Mensagens Flash (Sucesso/Erro) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (session()->has('sucesso')): ?>
            <div class="mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-100" role="alert">
                <?= session()->getFlashdata('sucesso') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->has('erro')): ?>
            <div class="mt-4 p-4 text-sm text-red-800 rounded-lg bg-red-100" role="alert">
                <?= session()->getFlashdata('erro') ?>
            </div>
        <?php endif; ?>
    </div>


    <!-- CONTEÚDO PRINCIPAL -->
    <main class="py-10 flex-grow">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- PONTO CRÍTICO: Onde o conteúdo da view (dashboard/index.php) é injetado -->
            <?= $this->renderSection('conteudo') ?>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">
                &copy; <?= date('Y') ?> DFC System - Desenvolvido por: José Ricardo.
            </p>
        </div>
    </footer>

    <?= $this->renderSection('scripts') ?>

</body>

</html>
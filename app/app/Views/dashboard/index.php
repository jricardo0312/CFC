<?php
// Define o layout principal que esta view deve herdar
$this->extend('layout/principal');

// Define a seção 'titulo'
$this->section('titulo');
?>
<?= esc($titulo) ?>
<?php $this->endSection(); ?>

<?php
// Define a seção 'conteudo' que será injetada no layout
$this->section('conteudo');
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-extrabold text-indigo-700 mb-2"><?= esc($titulo) ?></h1>
    <p class="text-xl text-gray-500 mb-8"><?= esc($subtitulo) ?></p>

    <!-- Cards de Módulos de CADASTRO (CRUD) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">

        <!-- Card: Lançamento de Transações (Próximo Módulo) -->
        <a href="#" class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center">
                <div class="bg-indigo-100 p-3 rounded-full text-indigo-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 3a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                        <path d="M14 10a1 1 0 100-2h-3a1 1 0 000 2h3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Lançamento de Transações</h2>
                    <p class="text-sm text-gray-500 mt-1">Contas a Pagar e Receber (PENDENTES).</p>
                </div>
            </div>
        </a>

        <!-- Card: Pessoas (Clientes, Fornecedores, Sócios) -->
        <a href="<?= route_to('pessoas_index') ?>" class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Cadastro de Pessoas</h2>
                    <p class="text-sm text-gray-500 mt-1">Clientes, Fornecedores e Sócios.</p>
                </div>
            </div>
        </a>

        <!-- Card: Categorias Financeiras (DFC) -->
        <a href="<?= route_to('categorias_index') ?>" class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center">
                <div class="bg-teal-100 p-3 rounded-full text-teal-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v5.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V7z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Mapeamento DFC</h2>
                    <p class="text-sm text-gray-500 mt-1">FCO, FCI e FCF (Plano de Contas).</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Seção de Relatórios e Analíticos (DFC) -->
    <h2 class="text-2xl font-bold text-gray-800 mt-8 mb-4 border-b pb-2">Relatórios e Fluxo de Caixa</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">

        <!-- Card: Relatório DFC (A ser implementado) -->
        <a href="#" class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-[1.02] border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Demonstração dos Fluxos de Caixa (DFC)</h2>
                    <p class="text-sm text-gray-500 mt-1">Análise consolidada (FCO, FCI, FCF).</p>
                </div>
            </div>
        </a>

        <!-- Card: Contas Pendentes (Dashboard Operacional) -->
        <a href="#" class="block bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 transform hover:scale-[1.02] border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="bg-yellow-100 p-3 rounded-full text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h.01a1 1 0 100-2H10zm3 0a1 1 0 000 2h.01a1 1 0 100-2H13z" clip-rule="evenodd" />
                        <path d="M8 12a1 1 0 100 2h.01a1 1 0 100-2H8zm3 0a1 1 0 100 2h.01a1 1 0 100-2H11zm3 0a1 1 0 100 2h.01a1 1 0 100-2H14z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Contas Pendentes</h2>
                    <p class="text-sm text-gray-500 mt-1">Itens a receber ou pagar (Regime de Competência).</p>
                </div>
            </div>
        </a>

    </div>
</div>

<?php $this->endSection(); ?>
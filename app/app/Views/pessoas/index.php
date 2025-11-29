<?php
$this->extend('layout/principal');
$this->section('conteudo');

// --- LÓGICA DE ORDENAÇÃO ---
$currentOrdem   = isset($ordem) ? $ordem : 'id';
$currentDirecao = isset($direcao) ? $direcao : 'asc';

$proximaDirId   = ($currentOrdem === 'id' && $currentDirecao === 'asc') ? 'desc' : 'asc';
$proximaDirNome = ($currentOrdem === 'nome' && $currentDirecao === 'asc') ? 'desc' : 'asc';

// Mantemos os parâmetros na URL
$linkId   = site_url("pessoas?ordem=id&direcao={$proximaDirId}");
$linkNome = site_url("pessoas?ordem=nome&direcao={$proximaDirNome}");

// Ícones de Ordenação
$setaCima   = '<span class="text-indigo-600 ml-1">▲</span>';
$setaBaixo  = '<span class="text-indigo-600 ml-1">▼</span>';
$setaNeutra = '<span class="text-gray-300 ml-1 opacity-50">⇅</span>';

$iconeId = ($currentOrdem === 'id') ? (($currentDirecao === 'asc') ? $setaCima : $setaBaixo) : $setaNeutra;
$iconeNome = ($currentOrdem === 'nome') ? (($currentDirecao === 'asc') ? $setaCima : $setaBaixo) : $setaNeutra;
?>

<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-300">
        <h1 class="text-3xl font-bold text-gray-800">
            <?= esc($titulo ?? 'Lista de Pessoas') ?>
        </h1>

        <a href="<?= site_url('pessoas/nova') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Nova Pessoa
        </a>
    </div>

    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <?= session()->getFlashdata('sucesso') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?= session()->getFlashdata('erro') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-lg overflow-x-auto mb-6">

        <?php if (empty($pessoas)): ?>
            <div class="p-6 text-center text-gray-500">
                Nenhuma pessoa cadastrada ainda.
            </div>
        <?php else: ?>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <a href="<?= $linkId ?>" class="block w-full h-full select-none" title="Ordenar por ID">
                                ID <?= $iconeId ?>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <a href="<?= $linkNome ?>" class="block w-full h-full select-none" title="Ordenar por Nome">
                                NOME <?= $iconeNome ?>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pessoas as $pessoa): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?= esc($pessoa['id']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                <?= esc($pessoa['nome']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <span class="text-xs font-bold text-gray-500 mr-1"><?= esc($pessoa['tipo_documento']) ?>:</span>
                                <?= esc($pessoa['documento']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?= esc($pessoa['email']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">

                                <a href="<?= site_url('pessoas/editar/' . $pessoa['id']) ?>" class="text-indigo-600 hover:text-indigo-900 inline-block transition-colors" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>

                                <form action="<?= site_url('pessoas/excluir/' . $pessoa['id']) ?>" method="post" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Excluir">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    </div>

    <div class="flex justify-center mt-4">
        <?= $pager->links('default', 'default_full') ?>
    </div>

    <style>
        .pagination {
            display: flex;
            list-style: none;
            gap: 0.5rem;
        }

        .pagination li a,
        .pagination li span {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            color: #4b5563;
            background-color: white;
            text-decoration: none;
        }

        .pagination li.active a,
        .pagination li.active span {
            background-color: #4f46e5;
            /* Indigo-600 */
            color: white;
            border-color: #4f46e5;
        }

        .pagination li a:hover {
            background-color: #f3f4f6;
        }
    </style>

</div>
<?php $this->endSection(); ?>
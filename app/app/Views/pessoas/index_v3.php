<?php
// Define o layout. SE O ARQUIVO 'app/Views/layout/principal.php' NÃO EXISTIR, VAI DAR TELA BRANCA.
$this->extend('layout/principal');

$this->section('conteudo');

// --- LÓGICA DE PREPARAÇÃO DOS LINKS ---
// Verifica se as variáveis existem. Se não, usa valores padrão para não quebrar a tela.
$currentOrdem   = isset($ordem) ? $ordem : 'id';
$currentDirecao = isset($direcao) ? $direcao : 'asc';

// Define a próxima direção para cada coluna
// Se clicar no ID e já estiver ASC, muda para DESC. Caso contrário, reseta para ASC.
$proximaDirId   = ($currentOrdem === 'id' && $currentDirecao === 'asc') ? 'desc' : 'asc';
$proximaDirNome = ($currentOrdem === 'nome' && $currentDirecao === 'asc') ? 'desc' : 'asc';

// Monta os links usando site_url para evitar erros de rotas nomeadas
$linkId   = site_url("pessoas?ordem=id&direcao={$proximaDirId}");
$linkNome = site_url("pessoas?ordem=nome&direcao={$proximaDirNome}");

// Define os ícones (Setinhas)
$setaCima  = '<span class="text-indigo-600 ml-1">▲</span>';
$setaBaixo = '<span class="text-indigo-600 ml-1">▼</span>';
$setaNeutra= '<span class="text-gray-300 ml-1 opacity-50">⇅</span>';

// Lógica de qual seta mostrar
$iconeId = $setaNeutra;
if ($currentOrdem === 'id') {
    $iconeId = ($currentDirecao === 'asc') ? $setaCima : $setaBaixo;
}

$iconeNome = $setaNeutra;
if ($currentOrdem === 'nome') {
    $iconeNome = ($currentDirecao === 'asc') ? $setaCima : $setaBaixo;
}
?>

<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-300">
        <h1 class="text-3xl font-bold text-gray-800">
            <?= esc($titulo ?? 'Lista de Pessoas') ?>
        </h1>
        
        <a href="<?= site_url('pessoas/nova') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 flex items-center">
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
    
    <?php if (session()->getFlashdata('erros')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
            <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                <li><?= esc($erro) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>


    <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
        
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
                                <?= esc($pessoa['documento']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?= esc($pessoa['email']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="<?= site_url('pessoas/editar/' . $pessoa['id']) ?>" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                
                                <form action="<?= site_url('pessoas/excluir/' . $pessoa['id']) ?>" method="post" class="inline-block" onsubmit="return confirm('Excluir este registro?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>

<?php $this->endSection(); ?>

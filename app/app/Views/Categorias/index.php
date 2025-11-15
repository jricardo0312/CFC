<?php
// Define o layout principal que esta view deve herdar
$this->extend('layout/principal');

// Define a seção 'conteudo' que será injetada no layout
$this->section('conteudo');
?>

<div class="container mx-auto px-4 py-8">

    <!-- Título e Botão de Cadastro -->
    <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-300">
        <h1 class="text-3xl font-bold text-gray-800">
            <?= esc($titulo) ?>
        </h1>
        <a href="<?= route_to('categorias_nova') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Nova Categoria
        </a>
    </div>

    <!-- Mensagens Flash (Sucesso ou Erro) -->
    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= session()->getFlashdata('sucesso') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= session()->getFlashdata('erro') ?></span>
        </div>
    <?php endif; ?>


    <!-- Tabela de Categorias -->
    <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
        <?php if (empty($categorias)): ?>

            <!-- Mensagem se não houver categorias -->
            <div class="p-6 text-center text-gray-500">
                Nenhuma categoria cadastrada ainda.
            </div>

        <?php else: ?>

            <!-- Tabela com os dados -->
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome da Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapeamento DFC</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?= esc($categoria['nome']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php
                                $fluxo = esc($categoria['tipo_fluxo']);
                                $class = 'bg-gray-200 text-gray-800';
                                if ($fluxo === 'FCO') $class = 'bg-blue-100 text-blue-800';
                                if ($fluxo === 'FCI') $class = 'bg-yellow-100 text-yellow-800';
                                if ($fluxo === 'FCF') $class = 'bg-green-100 text-green-800';
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $class ?>">
                                    <?= $fluxo ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">

                                <!-- Link de Edição -->
                                <a href="<?= route_to('categorias_editar', $categoria['id']) ?>" class="text-indigo-600 hover:text-indigo-900 transition duration-150" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-1.897 2.016l-2.73 2.73L9 12.593l-.707.707-.707-.707-1.414 1.414.707.707-.707.707-1.414 1.414 1.414 1.414 4.242-4.242 2.73-2.73L13.586 7.586a2 2 0 112.828 2.828l-5.657 5.657-3.535.707.707-3.535 5.657-5.657z" />
                                    </svg>
                                </a>

                                <!-- Formulário de Exclusão -->
                                <form action="<?= route_to('categorias_excluir', $categoria['id']) ?>" method="post" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria? Esta ação é irreversível.');">

                                    <!-- Proteção CSRF -->
                                    <?= csrf_field() ?>

                                    <!-- Define o método real como DELETE -->
                                    <input type="hidden" name="_method" value="DELETE">

                                    <button type="submit" class="text-red-600 hover:text-red-900 transition duration-150" title="Excluir">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm3 3a1 1 0 011-1h.01a1 1 0 110 2H11a1 1 0 01-1-1z" clip-rule="evenodd" />
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

</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
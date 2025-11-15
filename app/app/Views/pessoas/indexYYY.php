<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>

<div class="container mx-auto max-w-5xl px-4 py-8">

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2"><?= $title ?></h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100" role="alert">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100" role="alert">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <a href="<?= url_to('PessoasController::nova') ?>" class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300 mb-6">
        + Nova Pessoa
    </a>

    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-600 uppercase">Nome</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-600 uppercase">Tipo</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-600 uppercase">CPF/CNPJ</th>
                    <th class="py-3 px-4 text-center text-xs font-medium text-gray-600 uppercase">Ações</th>
                </tr>
            </thead>

            <!-- Continuação do arquivo index.php -->
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($pessoas)): ?>
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500 italic">
                            Nenhuma pessoa cadastrada.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pessoas as $pessoa): ?>
                        <tr>
                            <td class="py-4 px-4 text-sm font-medium text-gray-900"><?= esc($pessoa['nome']) ?></td>
                            <td class="py-4 px-4 text-sm text-gray-500">
                                <?php
                                $tipo_classes = [
                                    'CLIENTE' => 'bg-blue-100 text-blue-800',
                                    'FORNECEDOR' => 'bg-yellow-100 text-yellow-800',
                                    'SOCIO' => 'bg-green-100 text-green-800',
                                ];
                                $classe = $tipo_classes[$pessoa['tipo_pessoa']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $classe ?>">
                                    <?= esc($pessoa['tipo_pessoa']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500"><?= esc($pessoa['cpf_cnpj']) ?></td>
                            <td class="py-4 px-4 text-center">
                                <a href="<?= url_to('PessoasController::editar', $pessoa['id']) ?>" class="text-indigo-600 hover:text-indigo-900 font-medium mr-3">Editar</a>
                                <a href="<?= url_to('PessoasController::excluir', $pessoa['id']) ?>"
                                    class="text-red-600 hover:text-red-900 font-medium"
                                    onclick="return confirm('Tem certeza que deseja excluir esta pessoa? Verifique se ela não está sendo usada em transações.');">
                                    Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
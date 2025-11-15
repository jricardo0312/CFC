<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');

// Lógica para Edição:
// Se a variável $pessoa foi passada pelo Controller (método editar), usamos ela.
// Se não (método nova), $pessoa será null.
$is_edit = isset($pessoa);
?>

<div class="container mx-auto px-4 py-8 max-w-2xl">

    <h1 class="text-3xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-300">
        <?= esc($titulo) ?>
    </h1>

    <!-- Área de Exibição de Erros de Validação -->
    <?php if (session()->getFlashdata('erros')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Ocorreram erros no cadastro:</strong>
            <ul class="mt-2 list-disc list-inside">
                <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                    <li><?= esc($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulário de Cadastro/Edição -->
    <div class="bg-white p-6 rounded-xl shadow-lg">

        <!-- O formulário SEMPRE aponta para 'pessoas_salvar' -->
        <?= form_open(route_to('pessoas_salvar')) ?>

        <!-- CAMPO OCULTO DE ID (Fundamental para Edição) -->
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= esc($pessoa['id']) ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Coluna 1: Nome -->
            <div class="md:col-span-2">
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo (ou Razão Social) <span class="text-red-500">*</span></label>

                <!-- Preenche com old('nome') ou com $pessoa['nome'] (se for edição) -->
                <input type="text" name="nome" id="nome"
                    value="<?= old('nome', $pessoa['nome'] ?? '') ?>"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Coluna 1: Tipo de Documento -->
            <div>
                <label for="tipo_documento" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento <span class="text-red-500">*</span></label>
                <select name="tipo_documento" id="tipo_documento" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                    <!-- Lógica para selecionar o valor antigo (old) ou o valor do banco ($pessoa) -->
                    <?php $tipo_selecionado = old('tipo_documento', $pessoa['tipo_documento'] ?? ''); ?>

                    <option value="" <?= ($tipo_selecionado == '') ? 'selected' : '' ?>>Selecione...</option>
                    <option value="CPF" <?= ($tipo_selecionado == 'CPF') ? 'selected' : '' ?>>CPF</option>
                    <option value="CNPJ" <?= ($tipo_selecionado == 'CNPJ') ? 'selected' : '' ?>>CNPJ</option>
                </select>
            </div>

            <!-- Coluna 2: Documento -->
            <div>
                <label for="documento" class="block text-sm font-medium text-gray-700 mb-1">Número do Documento (Apenas números) <span class="text-red-500">*</span></label>
                <input type="text" name="documento" id="documento"
                    value="<?= old('documento', $pessoa['documento'] ?? '') ?>"
                    maxlength="14" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Coluna 1: E-mail -->
            <div class="md:col-span-2">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email"
                    value="<?= old('email', $pessoa['email'] ?? '') ?>"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="<?= route_to('pessoas_index') ?>" class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm hover:bg-gray-50 transition duration-300">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                <!-- O texto do botão muda se for edição -->
                <?= $is_edit ? 'Atualizar Pessoa' : 'Salvar Pessoa' ?>
            </button>
        </div>

        <?= form_close() ?>

    </div>
</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
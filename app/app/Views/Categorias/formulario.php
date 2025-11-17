<?php
// Define o layout principal que esta view deve herdar
$this->extend('layout/principal');

// Define a seção 'conteudo' que será injetada no layout
$this->section('conteudo');

// Lógica para Edição:
// Se a variável $categoria foi passada pelo Controller (método editar), usamos ela.
$is_edit = isset($categoria);
?>

<div class="container mx-auto px-4 py-8 max-w-2xl">

    <h1 class="text-3xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-300">
        <?= esc($titulo) ?>
    </h1>

    <!-- Área de Exibição de Erros de Validação -->
    <?php if (session()->getFlashdata('erros')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Ocorreram erros:</strong>
            <ul class="mt-2 list-disc list-inside">
                <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                    <li><?= esc($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulário de Cadastro/Edição -->
    <div class="bg-white p-6 rounded-xl shadow-lg">

        <!-- O formulário SEMPRE aponta para 'categorias_salvar' -->
        <?= form_open(route_to('categorias_salvar')) ?>

        <!-- CAMPO OCULTO DE ID (Fundamental para Edição) -->
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= esc($categoria['id']) ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-6">

            <!-- Nome da Categoria -->
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome da Categoria (Ex: Receita de Consultas, Aluguel do Escritório) <span class="text-red-500">*</span></label>

                <!-- Preenche com old('nome') ou com $categoria['nome'] (se for edição) -->
                <input type="text" name="nome" id="nome"
                    value="<?= old('nome', $categoria['nome'] ?? '') ?>"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Tipo de Fluxo DFC -->
            <div>
                <label for="tipo_fluxo" class="block text-sm font-medium text-gray-700 mb-1">Mapeamento DFC (Fluxo de Caixa) <span class="text-red-500">*</span></label>
                <select name="tipo_fluxo" id="tipo_fluxo" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                    <!-- Lógica para selecionar o valor antigo (old) ou o valor do banco ($categoria) -->
                    <?php $fluxo_selecionado = old('tipo_fluxo', $categoria['tipo_fluxo'] ?? ''); ?>

                    <option value="">Selecione o Tipo de Fluxo...</option>
                    <option value="FCO" <?= ($fluxo_selecionado == 'FCO') ? 'selected' : '' ?>>FCO - Operacional (Atividades do dia a dia)</option>
                    <option value="FCI" <?= ($fluxo_selecionado == 'FCI') ? 'selected' : '' ?>>FCI - Investimento (Compra/Venda de Ativos)</option>
                    <option value="FCF" <?= ($fluxo_selecionado == 'FCF') ? 'selected' : '' ?>>FCF - Financiamento (Dívidas, Capital e Dividendos)</option>
                </select>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="<?= route_to('categorias_index') ?>" class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm hover:bg-gray-50 transition duration-300">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                <!-- O texto do botão muda se for edição -->
                <?= $is_edit ? 'Atualizar Categoria' : 'Salvar Categoria' ?>
            </button>
        </div>

        <?= form_close() ?>

    </div>
</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
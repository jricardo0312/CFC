<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');

// Assume que $transacao está disponível para edição, se não, é um array vazio
$is_edit = isset($transacao);
?>

<div class="container mx-auto px-4 py-8 max-w-4xl">

    <h1 class="text-3xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-300">
        <?= esc($titulo) ?>
    </h1>

    <!-- Área de Exibição de Erros de Validação -->
    <?php if (session()->getFlashdata('erros')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Ocorreram erros de validação:</strong>
            <ul class="mt-2 list-disc list-inside">
                <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                    <li><?= esc($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulário de Transação -->
    <div class="bg-white p-8 rounded-xl shadow-lg">

        <!-- O formulário SEMPRE aponta para 'salvar_transacao' -->
        <?= form_open(route_to('salvar_transacao')) ?>

        <!-- CAMPO OCULTO DE ID (para Edição) -->
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= esc($transacao['id']) ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- 1. Tipo de Transação (PAGAR/RECEBER) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Transação <span class="text-red-500">*</span></label>
                <?php $tipo_selecionado = old('tipo', $transacao['tipo'] ?? 'RECEBER'); ?>
                <div class="flex space-x-4 mt-2">
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio text-green-600 h-4 w-4" name="tipo" value="RECEBER" <?= ($tipo_selecionado == 'RECEBER') ? 'checked' : '' ?>>
                        <span class="ml-2 text-gray-700 font-semibold text-green-600">A Receber</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio text-red-600 h-4 w-4" name="tipo" value="PAGAR" <?= ($tipo_selecionado == 'PAGAR') ? 'checked' : '' ?>>
                        <span class="ml-2 text-gray-700 font-semibold text-red-600">A Pagar</span>
                    </label>
                </div>
            </div>

            <!-- 2. Valor -->
            <div>
                <label for="valor" class="block text-sm font-medium text-gray-700 mb-1">Valor (R$) <span class="text-red-500">*</span></label>
                <input type="text" name="valor" id="valor"
                    value="<?= old('valor', number_format($transacao['valor'] ?? 0, 2, ',', '.') ?? '') ?>"
                    placeholder="Ex: 150,00"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- 3. Pessoa (Cliente/Fornecedor) -->
            <div>
                <label for="pessoa_id" class="block text-sm font-medium text-gray-700 mb-1">Pessoa (Cliente ou Fornecedor) <span class="text-red-500">*</span></label>
                <select name="pessoa_id" id="pessoa_id" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <?php $pessoa_selecionada = old('pessoa_id', $transacao['pessoa_id'] ?? ''); ?>
                    <option value="">Selecione a Pessoa...</option>
                    <?php foreach ($pessoas as $pessoa): ?>
                        <option value="<?= esc($pessoa['id']) ?>" <?= ($pessoa_selecionada == $pessoa['id']) ? 'selected' : '' ?>>

                            <!-- 
                                    AQUI ESTÁ A CORREÇÃO:
                                    Trocamos $pessoa['tipo_pessoa'] por $pessoa['tipo_documento']
                                -->
                            <?= esc($pessoa['nome']) ?> (<?= esc($pessoa['tipo_documento']) ?>)

                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 4. Categoria (DFC) -->
            <div>
                <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria Financeira (Mapeamento DFC) <span class="text-red-500">*</span></label>
                <select name="categoria_id" id="categoria_id" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <?php $categoria_selecionada = old('categoria_id', $transacao['categoria_id'] ?? ''); ?>
                    <option value="">Selecione a Categoria...</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= esc($categoria['id']) ?>" <?= ($categoria_selecionada == $categoria['id']) ? 'selected' : '' ?>>
                            [<?= esc($categoria['tipo_fluxo']) ?>] <?= esc($categoria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 5. Data de Vencimento -->
            <div class="md:col-span-2">
                <label for="data_vencimento" class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento <span class="text-red-500">*</span></label>
                <input type="date" name="data_vencimento" id="data_vencimento"
                    value="<?= old('data_vencimento', $transacao['data_vencimento'] ?? '') ?>"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- 6. Descrição -->
            <div class="md:col-span-2">
                <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição Breve <span class="text-red-500">*</span></label>
                <input type="text" name="descricao" id="descricao"
                    value="<?= old('descricao', $transacao['descricao'] ?? '') ?>"
                    placeholder="Ex: Consulta Psicológica - Sessão 5"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

        </div>

        <!-- Botões de Ação -->
        <div class="mt-10 flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="<?= route_to('financeiro_index') ?>" class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm hover:bg-gray-50 transition duration-300">
                Voltar / Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                <!-- O texto do botão muda se for edição -->
                <?= $is_edit ? 'Atualizar Transação' : 'Registrar Transação' ?>
            </button>
        </div>

        <?= form_close() ?>

    </div>
</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
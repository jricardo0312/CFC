<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>

<div class="container mx-auto max-w-xl px-4 py-8">

    <?php
    // 1. Configura a ação do formulário (sempre aponta para o método salvar)
    $action = url_to('PessoasController::salvar');

    // 2. Determina se estamos em modo de edição
    $is_edit = isset($pessoa) && !empty($pessoa) && isset($pessoa['id']);

    // 3. Define o array de dados (usa $pessoa se for edição, ou array vazio para novo/validação)
    $dados = $is_edit ? $pessoa : [];
    ?>

    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2"><?= esc($title) ?></h1>

    <!-- Exibe a mensagem de erro de validação global se houver -->
    <?php if (isset($validation)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Erro de Validação!</strong>
            <span class="block sm:inline">Por favor, verifique e corrija os campos destacados abaixo.</span>
        </div>
    <?php endif; ?>

    <form action="<?= $action ?>" method="post" class="space-y-6 bg-white p-6 rounded-xl shadow-lg">

        <!-- Campo oculto para ID (apenas em edição) -->
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= esc($pessoa['id']) ?>">
        <?php endif; ?>

        <!-- Token CSRF -->
        <?= csrf_field() ?>

        <!-- Nome da Pessoa -->
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo / Razão Social <span class="text-red-500">*</span></label>
            <input type="text" id="nome" name="nome"
                value="<?= old('nome', $dados['nome'] ?? '') ?>"
                class="mt-1 block w-full px-4 py-2 border <?= (isset($validation) && $validation->hasError('nome')) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required>
            <?php if (isset($validation) && $validation->hasError('nome')): ?>
                <p class="mt-1 text-xs text-red-500"><?= $validation->getError('nome') ?></p>
            <?php endif; ?>
        </div>

        <!-- Tipo de Pessoa -->
        <div>
            <label for="tipo_pessoa" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pessoa <span class="text-red-500">*</span></label>
            <select id="tipo_pessoa" name="tipo_pessoa"
                class="mt-1 block w-full px-4 py-2 border <?= (isset($validation) && $validation->hasError('tipo_pessoa')) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required>
                <option value="">Selecione o Tipo</option>
                <?php
                $selected_tipo = old('tipo_pessoa', $dados['tipo_pessoa'] ?? '');
                $tipos = ['CLIENTE', 'FORNECEDOR', 'SOCIO'];
                ?>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?= $tipo ?>" <?= $selected_tipo === $tipo ? 'selected' : '' ?>>
                        <?= ucfirst(strtolower($tipo)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($validation) && $validation->hasError('tipo_pessoa')): ?>
                <p class="mt-1 text-xs text-red-500"><?= $validation->getError('tipo_pessoa') ?></p>
            <?php endif; ?>
        </div>

        <!-- CPF/CNPJ -->
        <div>
            <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ <span class="text-red-500">*</span></label>
            <input type="text" id="cpf_cnpj" name="cpf_cnpj"
                value="<?= old('cpf_cnpj', $dados['cpf_cnpj'] ?? '') ?>"
                class="mt-1 block w-full px-4 py-2 border <?= (isset($validation) && $validation->hasError('cpf_cnpj')) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Apenas números (Ex: 12345678901)"
                required>
            <?php if (isset($validation) && $validation->hasError('cpf_cnpj')): ?>
                <p class="mt-1 text-xs text-red-500"><?= $validation->getError('cpf_cnpj') ?></p>
            <?php endif; ?>
        </div>

        <!-- E-mail -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
            <input type="email" id="email" name="email"
                value="<?= old('email', $dados['email'] ?? '') ?>"
                class="mt-1 block w-full px-4 py-2 border <?= (isset($validation) && $validation->hasError('email')) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <?php if (isset($validation) && $validation->hasError('email')): ?>
                <p class="mt-1 text-xs text-red-500"><?= $validation->getError('email') ?></p>
            <?php endif; ?>
        </div>

        <!-- Telefone -->
        <div>
            <label for="telefone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
            <input type="text" id="telefone" name="telefone"
                value="<?= old('telefone', $dados['telefone'] ?? '') ?>"
                class="mt-1 block w-full px-4 py-2 border <?= (isset($validation) && $validation->hasError('telefone')) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="(99) 99999-9999">
            <?php if (isset($validation) && $validation->hasError('telefone')): ?>
                <p class="mt-1 text-xs text-red-500"><?= $validation->getError('telefone') ?></p>
            <?php endif; ?>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end space-x-4 pt-4">
            <a href="<?= url_to('PessoasController::index') ?>" class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm hover:bg-gray-50 transition duration-300">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                <?= $is_edit ? 'Salvar Alterações' : 'Cadastrar Pessoa' ?>
            </button>
        </div>

    </form>
</div>

<?= $this->endSection() ?>
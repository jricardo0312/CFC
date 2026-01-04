<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>

<?php
// 1. Configura a ação do formulário
$action = url_to('PessoasController::salvar');

// 2. Determina se estamos em modo de edição
$is_edit = isset($pessoa) && !empty($pessoa) && isset($pessoa['id']);

// 3. Define o array de dados
$dados = $is_edit ? $pessoa : [];
?>

<div class="columns is-centered">
    <div class="column is-8-tablet is-6-desktop">

        <div class="block mb-5">
            <h1 class="title is-3 has-text-grey-darker"><?= esc($title) ?></h1>
            <p class="subtitle is-6 has-text-grey">Preencha os campos abaixo.</p>
        </div>

        <?php if (isset($validation)): ?>
            <div class="notification is-danger is-light">
                <button class="delete"></button>
                <strong>Erro de Validação!</strong>
                <p>Por favor, verifique e corrija os campos destacados abaixo.</p>
            </div>
        <?php endif; ?>

        <div class="box">
            <form action="<?= $action ?>" method="post">

                <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?= esc($pessoa['id']) ?>">
                <?php endif; ?>

                <?= csrf_field() ?>

                <div class="field">
                    <label class="label" for="nome">Nome Completo / Razão Social <span class="has-text-danger">*</span></label>
                    <div class="control has-icons-left">
                        <input type="text" id="nome" name="nome"
                            value="<?= old('nome', $dados['nome'] ?? '') ?>"
                            class="input <?= (isset($validation) && $validation->hasError('nome')) ? 'is-danger' : '' ?>"
                            placeholder="Ex: João Silva ou Empresa LTDA">
                        <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('nome')): ?>
                        <p class="help is-danger"><?= $validation->getError('nome') ?></p>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="label" for="tipo_pessoa">Tipo de Pessoa <span class="has-text-danger">*</span></label>
                    <div class="control has-icons-left">
                        <div class="select is-fullwidth <?= (isset($validation) && $validation->hasError('tipo_pessoa')) ? 'is-danger' : '' ?>">
                            <select id="tipo_pessoa" name="tipo_pessoa">
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
                        </div>
                        <span class="icon is-small is-left">
                            <i class="fas fa-users"></i>
                        </span>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('tipo_pessoa')): ?>
                        <p class="help is-danger"><?= $validation->getError('tipo_pessoa') ?></p>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="label" for="cpf_cnpj">CPF/CNPJ <span class="has-text-danger">*</span></label>
                    <div class="control has-icons-left">
                        <input type="text" id="cpf_cnpj" name="cpf_cnpj"
                            value="<?= old('cpf_cnpj', $dados['cpf_cnpj'] ?? '') ?>"
                            class="input <?= (isset($validation) && $validation->hasError('cpf_cnpj')) ? 'is-danger' : '' ?>"
                            placeholder="Apenas números">
                        <span class="icon is-small is-left">
                            <i class="fas fa-id-card"></i>
                        </span>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('cpf_cnpj')): ?>
                        <p class="help is-danger"><?= $validation->getError('cpf_cnpj') ?></p>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="label" for="email">E-mail</label>
                    <div class="control has-icons-left">
                        <input type="email" id="email" name="email"
                            value="<?= old('email', $dados['email'] ?? '') ?>"
                            class="input <?= (isset($validation) && $validation->hasError('email')) ? 'is-danger' : '' ?>"
                            placeholder="exemplo@email.com">
                        <span class="icon is-small is-left">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('email')): ?>
                        <p class="help is-danger"><?= $validation->getError('email') ?></p>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="label" for="telefone">Telefone</label>
                    <div class="control has-icons-left">
                        <input type="text" id="telefone" name="telefone"
                            value="<?= old('telefone', $dados['telefone'] ?? '') ?>"
                            class="input <?= (isset($validation) && $validation->hasError('telefone')) ? 'is-danger' : '' ?>"
                            placeholder="(99) 99999-9999">
                        <span class="icon is-small is-left">
                            <i class="fas fa-phone"></i>
                        </span>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('telefone')): ?>
                        <p class="help is-danger"><?= $validation->getError('telefone') ?></p>
                    <?php endif; ?>
                </div>

                <hr>

                <div class="field is-grouped is-grouped-right">
                    <div class="control">
                        <a href="<?= url_to('PessoasController::index') ?>" class="button is-light">
                            Cancelar
                        </a>
                    </div>
                    <div class="control">
                        <button type="submit" class="button is-link">
                            <span class="icon is-small">
                                <i class="fas fa-save"></i>
                            </span>
                            <span><?= $is_edit ? 'Salvar Alterações' : 'Cadastrar Pessoa' ?></span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
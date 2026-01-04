<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');

// Lógica para Edição
$is_edit = isset($pessoa);
?>

<div class="columns is-centered">
    <div class="column is-8-tablet is-8-desktop">

        <div class="block mb-5">
            <h1 class="title is-3 has-text-grey-darker"><?= esc($titulo) ?></h1>
            <p class="subtitle is-6 has-text-grey">Preencha os dados cadastrais da pessoa (Cliente/Fornecedor)</p>
        </div>

        <?php if (session()->getFlashdata('erros')): ?>
            <div class="notification is-danger is-light">
                <button class="delete"></button>
                <strong>Ocorreram erros no cadastro:</strong>
                <ul class="mt-2 ml-4" style="list-style-type: disc;">
                    <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                        <li><?= esc($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="box">

            <?= form_open(route_to('pessoas_salvar')) ?>

            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= esc($pessoa['id']) ?>">
            <?php endif; ?>

            <div class="field">
                <label class="label">Nome Completo (ou Razão Social) <span class="has-text-danger">*</span></label>
                <div class="control has-icons-left">
                    <input type="text" name="nome" id="nome"
                        value="<?= old('nome', $pessoa['nome'] ?? '') ?>"
                        class="input" placeholder="Ex: João da Silva ou Empresa X">
                    <span class="icon is-small is-left">
                        <i class="fas fa-user"></i>
                    </span>
                </div>
            </div>

            <div class="columns">

                <div class="column is-one-third">
                    <div class="field">
                        <label class="label">Tipo de Cliente <span class="has-text-danger">*</span></label>
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth">
                                <select name="tipo_cliente" id="tipo_cliente">
                                    <?php $tipo_selecionado = old('tipo_cliente', $cliente['tipo_cliente'] ?? ''); ?>

                                    <option value="" <?= ($tipo_selecionado == '') ? 'selected' : '' ?>>Selecione...</option>
                                    <option value="Ana" <?= ($tipo_selecionado == 'Ana') ? 'selected' : '' ?>>Ana Cristina</option>
                                    <option value="Ricardo" <?= ($tipo_selecionado == 'Ricardo') ? 'selected' : '' ?>>José Ricardo</option>
                                    <option value="Salutem" <?= ($tipo_selecionado == 'Salutem') ? 'selected' : '' ?>>Salutem Terapias</option>
                                </select>
                            </div>
                            <span class="icon is-small is-left">
                                <i class="fas fa-list"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="column is-one-third">
                    <div class="field">
                        <label class="label">Tipo de Documento <span class="has-text-danger">*</span></label>
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth">
                                <select name="tipo_documento" id="tipo_documento">
                                    <?php $tipo_selecionado = old('tipo_documento', $pessoa['tipo_documento'] ?? ''); ?>

                                    <option value="" <?= ($tipo_selecionado == '') ? 'selected' : '' ?>>Selecione...</option>
                                    <option value="CPF" <?= ($tipo_selecionado == 'CPF') ? 'selected' : '' ?>>CPF</option>
                                    <option value="CNPJ" <?= ($tipo_selecionado == 'CNPJ') ? 'selected' : '' ?>>CNPJ</option>
                                </select>
                            </div>
                            <span class="icon is-small is-left">
                                <i class="fas fa-list"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="column is-one-third">
                    <div class="field">
                        <label class="label">Número do Documento <span class="has-text-danger">*</span></label>
                        <div class="control has-icons-left">
                            <input type="text" name="documento" id="documento"
                                value="<?= old('documento', $pessoa['documento'] ?? '') ?>"
                                maxlength="14" class="input" placeholder="Apenas números">
                            <span class="icon is-small is-left">
                                <i class="fas fa-id-card"></i>
                            </span>
                        </div>
                        <p class="help">Digite sem pontos ou traços.</p>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label">E-mail <span class="has-text-danger">*</span></label>
                <div class="control has-icons-left">
                    <input type="email" name="email" id="email"
                        value="<?= old('email', $pessoa['email'] ?? '') ?>"
                        class="input" placeholder="exemplo@email.com">
                    <span class="icon is-small is-left">
                        <i class="fas fa-envelope"></i>
                    </span>
                </div>
            </div>

            <hr>

            <div class="field is-grouped is-grouped-right">
                <div class="control">
                    <a href="<?= route_to('pessoas_index') ?>" class="button is-light">
                        Cancelar
                    </a>
                </div>
                <div class="control">
                    <button type="submit" class="button is-link">
                        <span class="icon is-small">
                            <i class="fas fa-save"></i>
                        </span>
                        <span><?= $is_edit ? 'Atualizar Pessoa' : 'Salvar Pessoa' ?></span>
                    </button>
                </div>
            </div>

            <?= form_close() ?>

        </div>
    </div>
</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
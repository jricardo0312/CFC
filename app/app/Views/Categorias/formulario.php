<?php
// Define o layout principal que esta view deve herdar
$this->extend('layout/principal');

// Define a seção 'conteudo' que será injetada no layout
$this->section('conteudo');

// Lógica para Edição:
$is_edit = isset($categoria);
?>

<div class="columns is-centered">
    <div class="column is-8-tablet is-6-desktop">

        <div class="block mb-5">
            <h1 class="title is-3 has-text-grey-darker"><?= esc($titulo) ?></h1>
            <p class="subtitle is-6 has-text-grey">Preencha os dados abaixo</p>
        </div>

        <?php if (session()->getFlashdata('erros')): ?>
            <div class="notification is-danger is-light">
                <button class="delete"></button> <strong>Ocorreram erros:</strong>
                <div class="content mt-2">
                    <ul>
                        <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                            <li><?= esc($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div class="box">

            <?= form_open(route_to('categorias_salvar')) ?>

            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= esc($categoria['id']) ?>">
            <?php endif; ?>

            <div class="field">
                <label class="label">Nome da Categoria <span class="has-text-danger">*</span></label>
                <div class="control has-icons-left">
                    <input class="input" type="text" name="nome" id="nome"
                        placeholder="Ex: Receita de Consultas"
                        value="<?= old('nome', $categoria['nome'] ?? '') ?>">

                    <span class="icon is-small is-left">
                        <i class="fas fa-tag"></i>
                    </span>
                </div>
                <p class="help">Ex: Aluguel, Consultas, Materiais.</p>
            </div>

            <div class="field">
                <label class="label">Mapeamento DFC (Fluxo de Caixa) <span class="has-text-danger">*</span></label>
                <div class="control has-icons-left">
                    <div class="select is-fullwidth">
                        <select name="tipo_fluxo" id="tipo_fluxo">
                            <?php $fluxo_selecionado = old('tipo_fluxo', $categoria['tipo_fluxo'] ?? ''); ?>

                            <option value="">Selecione o Tipo de Fluxo...</option>
                            <option value="FCO" <?= ($fluxo_selecionado == 'FCO') ? 'selected' : '' ?>>FCO - Operacional (Dia a dia)</option>
                            <option value="FCI" <?= ($fluxo_selecionado == 'FCI') ? 'selected' : '' ?>>FCI - Investimento (Ativos)</option>
                            <option value="FCF" <?= ($fluxo_selecionado == 'FCF') ? 'selected' : '' ?>>FCF - Financiamento (Dívidas/Sócios)</option>
                        </select>
                    </div>
                    <span class="icon is-small is-left">
                        <i class="fas fa-chart-pie"></i>
                    </span>
                </div>
            </div>

            <hr>

            <div class="field is-grouped is-grouped-right">
                <div class="control">
                    <a href="<?= route_to('categorias_index') ?>" class="button is-light">
                        Cancelar
                    </a>
                </div>
                <div class="control">
                    <button type="submit" class="button is-link">
                        <span class="icon is-small">
                            <i class="fas fa-save"></i>
                        </span>
                        <span><?= $is_edit ? 'Atualizar' : 'Salvar' ?></span>
                    </button>
                </div>
            </div>

            <?= form_close() ?>

        </div>
    </div>
</div>

<?php $this->endSection(); ?>
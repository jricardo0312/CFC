<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');

// Assume que $transacao está disponível para edição, se não, é um array vazio
$is_edit = isset($transacao);
?>

<div class="columns is-centered">
    <div class="column is-10-desktop is-full-tablet">

        <div class="block mb-5">
            <h1 class="title is-3 has-text-grey-darker"><?= esc($titulo) ?></h1>
            <p class="subtitle is-6 has-text-grey">Preencha os detalhes da movimentação financeira.</p>
        </div>

        <?php if (session()->getFlashdata('erros')): ?>
            <div class="notification is-danger is-light">
                <button class="delete"></button>
                <strong>Ocorreram erros de validação:</strong>
                <ul class="mt-2 ml-4" style="list-style-type: disc;">
                    <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                        <li><?= esc($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="box">

            <?= form_open(route_to('salvar_transacao')) ?>

            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= esc($transacao['id']) ?>">
            <?php endif; ?>

            <div class="columns is-multiline">

                <div class="column is-half">
                    <div class="field">
                        <label class="label">Tipo de Transação <span class="has-text-danger">*</span></label>
                        <div class="control">
                            <?php $tipo_selecionado = old('tipo', $transacao['tipo'] ?? 'RECEBER'); ?>

                            <label class="radio mr-4">
                                <input type="radio" name="tipo" value="RECEBER" <?= ($tipo_selecionado == 'RECEBER') ? 'checked' : '' ?>>
                                <span class="tag is-success is-light ml-1 font-bold">
                                    <span class="icon is-small mr-1"><i class="fas fa-arrow-up"></i></span>
                                    A Receber
                                </span>
                            </label>

                            <label class="radio">
                                <input type="radio" name="tipo" value="PAGAR" <?= ($tipo_selecionado == 'PAGAR') ? 'checked' : '' ?>>
                                <span class="tag is-danger is-light ml-1 font-bold">
                                    <span class="icon is-small mr-1"><i class="fas fa-arrow-down"></i></span>
                                    A Pagar
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="column is-half">
                    <div class="field">
                        <label class="label" for="valor">Valor (R$) <span class="has-text-danger">*</span></label>
                        <div class="control has-icons-left">
                            <input type="text" name="valor" id="valor"
                                value="<?= old('valor', number_format($transacao['valor'] ?? 0, 2, ',', '.') ?? '') ?>"
                                class="input" placeholder="Ex: 150,00">
                            <span class="icon is-small is-left">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="column is-half">
                    <div class="field">
                        <label class="label" for="pessoa_id">Pessoa (Cliente/Fornecedor) <span class="has-text-danger">*</span></label>
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth">
                                <select name="pessoa_id" id="pessoa_id">
                                    <?php $pessoa_selecionada = old('pessoa_id', $transacao['pessoa_id'] ?? ''); ?>
                                    <option value="">Selecione a Pessoa...</option>
                                    <?php foreach ($pessoas as $pessoa): ?>
                                        <option value="<?= esc($pessoa['id']) ?>" <?= ($pessoa_selecionada == $pessoa['id']) ? 'selected' : '' ?>>
                                            <?= esc($pessoa['nome']) ?> (<?= esc($pessoa['tipo_documento']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="column is-half">
                    <div class="field">
                        <label class="label" for="categoria_id">Categoria (Mapeamento DFC) <span class="has-text-danger">*</span></label>
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth">
                                <select name="categoria_id" id="categoria_id">
                                    <?php $categoria_selecionada = old('categoria_id', $transacao['categoria_id'] ?? ''); ?>
                                    <option value="">Selecione a Categoria...</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= esc($categoria['id']) ?>" <?= ($categoria_selecionada == $categoria['id']) ? 'selected' : '' ?>>
                                            [<?= esc($categoria['tipo_fluxo']) ?>] <?= esc($categoria['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <span class="icon is-small is-left">
                                <i class="fas fa-tags"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <label class="label" for="data_vencimento">Data de Vencimento <span class="has-text-danger">*</span></label>
                        <div class="control has-icons-left">
                            <input type="date" name="data_vencimento" id="data_vencimento"
                                value="<?= old('data_vencimento', $transacao['data_vencimento'] ?? '') ?>"
                                class="input">
                            <span class="icon is-small is-left">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="column is-full">
                    <div class="field">
                        <label class="label" for="descricao">Descrição Breve <span class="has-text-danger">*</span></label>
                        <div class="control has-icons-left">
                            <input type="text" name="descricao" id="descricao"
                                value="<?= old('descricao', $transacao['descricao'] ?? '') ?>"
                                class="input" placeholder="Ex: Consulta Psicológica - Sessão 5">
                            <span class="icon is-small is-left">
                                <i class="fas fa-align-left"></i>
                            </span>
                        </div>
                    </div>
                </div>

            </div>
            <hr>

            <div class="field is-grouped is-grouped-right">
                <div class="control">
                    <a href="<?= route_to('financeiro_index') ?>" class="button is-light">
                        Voltar / Cancelar
                    </a>
                </div>
                <div class="control">
                    <button type="submit" class="button is-link">
                        <span class="icon is-small">
                            <i class="fas fa-save"></i>
                        </span>
                        <span><?= $is_edit ? 'Atualizar Transação' : 'Registrar Transação' ?></span>
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
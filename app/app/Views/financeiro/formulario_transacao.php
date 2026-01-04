<?= $this->extend('layout/principal') ?>

<?= $this->section('titulo') ?> <?= $title ?? 'Nova Transação' ?> <?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.2.2/css/tom-select.css" rel="stylesheet" />

<style>
    /* Ajuste para fazer o Tom Select parecer um input nativo do Bulma */
    .ts-control {
        border-radius: 4px;
        /* Igual ao Bulma default */
        border-color: #dbdbdb;
        padding: 8px 12px;
        box-shadow: inset 0 0.0625em 0.125em rgba(10, 10, 10, 0.05);
    }

    .ts-wrapper.focus .ts-control {
        border-color: #485fc7;
        /* Cor Primary/Link do Bulma */
        box-shadow: 0 0 0 0.125em rgba(72, 95, 199, 0.25);
    }

    /* Corrige ícone caindo por cima do texto no select */
    .control.has-icons-left .ts-wrapper {
        padding-left: 0;
    }

    .control.has-icons-left .ts-control {
        padding-left: 2.5em;
        /* Espaço para o ícone */
    }
</style>

<div class="columns is-centered">
    <div class="column is-8-desktop is-10-tablet">

        <div class="block mb-6">
            <h1 class="title is-3 has-text-grey-darker">Nova Transação</h1>
            <p class="subtitle is-6 has-text-grey">As transações iniciam como <strong>PENDENTES</strong>.</p>
        </div>

        <?php if (isset($validation)): ?>
            <div class="notification is-danger is-light">
                <button class="delete"></button>
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <div class="box">
            <form action="<?= url_to('FinanceiroController::salvarTransacao') ?>" method="post">
                <?= csrf_field() ?>

                <div class="field">
                    <label class="label">Tipo de Movimentação</label>
                    <div class="control has-icons-left">
                        <div class="select is-fullwidth">
                            <select name="tipo" id="tipo">
                                <option value="">-- Selecione --</option>
                                <option value="RECEBER" <?= set_select('tipo', 'RECEBER') ?>>Conta a Receber (Entrada)</option>
                                <option value="PAGAR" <?= set_select('tipo', 'PAGAR') ?>>Conta a Pagar (Saída)</option>
                            </select>
                        </div>
                        <span class="icon is-small is-left">
                            <i class="fas fa-exchange-alt"></i>
                        </span>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Categoria (DFC)</label>
                    <div class="control has-icons-left">
                        <div class="select is-fullwidth">
                            <select name="categoria_id" id="categoria_id">
                                <option value="">-- Selecione --</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= set_select('categoria_id', $cat['id']) ?>>
                                        [<?= $cat['tipo_fluxo'] ?>] <?= esc($cat['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <span class="icon is-small is-left">
                            <i class="fas fa-tag"></i>
                        </span>
                    </div>
                    <p class="help">O código [FCO/FCI/FCF] define a posição no relatório financeiro.</p>
                </div>

                <div class="field">
                    <label class="label">Pessoa Envolvida</label>
                    <div class="control has-icons-left">
                        <select name="pessoa_id" id="pessoa_id" placeholder="Digite para buscar..." autocomplete="off">
                            <option value="">Selecione ou digite...</option>
                            <?php foreach ($pessoas as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= set_select('pessoa_id', $p['id']) ?>>
                                    [<?= $p['tipo_pessoa'] ?>] <?= esc($p['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="icon is-small is-left" style="z-index: 10;">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-half">
                        <div class="field">
                            <label class="label">Valor (R$)</label>
                            <div class="control has-icons-left">
                                <input type="number" step="0.01" name="valor" id="valor"
                                    class="input" placeholder="0,00"
                                    value="<?= set_value('valor') ?>">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="column is-half">
                        <div class="field">
                            <label class="label">Vencimento (Competência)</label>
                            <div class="control has-icons-left">
                                <input type="date" name="data_vencimento" id="data_vencimento"
                                    class="input"
                                    value="<?= set_value('data_vencimento') ?>">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Descrição / Histórico</label>
                    <div class="control">
                        <textarea name="descricao" id="descricao" class="textarea" placeholder="Detalhes da transação..."><?= set_value('descricao') ?></textarea>
                    </div>
                </div>

                <hr>

                <div class="field is-grouped is-grouped-right">
                    <div class="control">
                        <a href="<?= url_to('FinanceiroController::index') ?>" class="button is-light">
                            Cancelar
                        </a>
                    </div>
                    <div class="control">
                        <button type="submit" class="button is-link">
                            <span class="icon is-small">
                                <i class="fas fa-check"></i>
                            </span>
                            <span>Salvar Transação</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.2.2/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Inicialização do Tom Select com a mesma lógica anterior
        new TomSelect("#pessoa_id", {
            create: false,
            searchField: ['text'],
            sortField: [{
                    field: "$score",
                    direction: "desc"
                },
                {
                    field: "text",
                    direction: "asc"
                }
            ],
            placeholder: "Digite para buscar...",
            diacritics: true,
            render: {
                option: function(data, escape) {
                    return '<div class="py-2 px-2">' + escape(data.text) + '</div>';
                },
                no_results: function(data, escape) {
                    return '<div class="no-results p-2 has-text-grey">Nenhum resultado para "' + escape(data.input) + '"</div>';
                }
            }
        });

    });
</script>

<?= $this->endSection() ?>
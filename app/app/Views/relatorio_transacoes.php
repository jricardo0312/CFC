<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');
?>

<!-- Estilos para Otimizar a Impressão (Mantidos) -->
<style>
    @media print {

        /* Esconde elementos de navegação do site que não queremos no papel */
        .navbar,
        .hero,
        .footer,
        .buttons,
        .notification,
        form {
            display: none !important;
        }

        /* Ajustes da tabela para caber na folha */
        .table {
            font-size: 8pt !important;
            width: 100% !important;
        }

        /* Remove sombras e bordas do container principal */
        .box {
            box-shadow: none !important;
            border: none !important;
        }

        /* Otimização de margens */
        .section {
            padding: 0 !important;
        }

        /* Ajuste para quebrar texto longo */
        td {
            word-wrap: break-word !important;
        }

        /* Forçar exibição do nível (tabela de totais) */
        .level {
            display: flex !important;
        }
    }
</style>

<div class="columns is-centered">
    <div class="column is-12-widescreen">

        <div class="block mb-6 has-text-centered">
            <h1 class="title is-3 has-text-link-dark is-uppercase" style="border-bottom: 4px solid #3273dc; display: inline-block; padding-bottom: 10px;">
                <span>Relatório por Transações</span>
            </h1>
        </div>

        <div class="box is-hidden-print has-background-white-ter">
            <?= form_open(site_url('relatorio'), ['method' => 'get']) ?>

            <div class="columns is-align-items-end">

                <div class="column">
                    <div class="field">
                        <label class="label">Data Início (Caixa)</label>
                        <div class="control has-icons-left">
                            <input type="date" name="data_inicio" value="<?= esc($data_inicio) ?>" required class="input">
                            <span class="icon is-small is-left"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                </div>

                <div class="column">
                    <div class="field">
                        <label class="label">Data Fim (Caixa)</label>
                        <div class="control has-icons-left">
                            <input type="date" name="data_fim" value="<?= esc($data_fim) ?>" required class="input">
                            <span class="icon is-small is-left"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                </div>

                <div class="column is-narrow">
                    <div class="field">
                        <div class="control">
                            <button type="submit" class="button is-link has-text-weight-semibold">
                                Filtrar Relatório
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <?= form_close() ?>
        </div>

        <div class="level is-hidden-print mb-5">
            <div class="level-left"></div>
            <div class="level-right">
                <div class="buttons">
                    <button onclick="window.print()" class="button is-dark">
                        <span class="icon"><i class="fas fa-print"></i></span>
                        <span>Imprimir</span>
                    </button>

                    <?= form_open(site_url('relatorio/exportarCsv'), ['method' => 'get', 'class' => 'is-inline']) ?>
                    <input type="hidden" name="data_inicio" value="<?= esc($data_inicio) ?>">
                    <input type="hidden" name="data_fim" value="<?= esc($data_fim) ?>">
                    <button type="submit" class="button is-success">
                        <span class="icon"><i class="fas fa-file-csv"></i></span>
                        <span>Exportar CSV</span>
                    </button>
                    <?= form_close() ?>
                </div>
            </div>
        </div>

        <div class="is-hidden-tablet is-visible-print-block has-text-centered mb-4">
            <h2 class="title is-4">Relatório de Transações</h2>
            <p class="subtitle is-6">Período: <?= esc($periodo_txt) ?></p>
        </div>

        <?php
        // NOVO CÁLCULO: Saldo final do período é Saldo Inicial + Entradas - Saídas
        $saldoFinalTotal = $saldo_inicial;
        ?>

        <?php if (!empty($transacoes) || $saldo_inicial !== 0.00): ?>

            <!-- EXIBIÇÃO DO SALDO INICIAL -->
            <div class="notification is-light has-background-info-light has-text-info-dark has-text-weight-bold mb-4 p-3">
                <div class="level is-mobile">
                    <div class="level-left">
                        <p class="is-size-6">SALDO ANTERIOR AO PERÍODO (CAIXA):</p>
                    </div>
                    <div class="level-right">
                        <p class="is-size-6">
                            <span class="<?= ($saldo_inicial >= 0) ? 'has-text-success-dark' : 'has-text-danger-dark' ?>">
                                R$ <?= number_format($saldo_inicial, 2, ',', '.') ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="box p-0" style="overflow-x: auto;">
                <table class="table is-fullwidth is-striped is-hoverable is-bordered">
                    <thead>
                        <tr class="has-background-link has-text-white">
                            <th class="has-text-white is-size-7">ID</th>
                            <th class="has-text-white is-size-7">Tipo</th>
                            <th class="has-text-white is-size-7 has-text-right">Valor</th>
                            <th class="has-text-white is-size-7">Vencimento</th>
                            <th class="has-text-white is-size-7">Data Caixa</th>
                            <th class="has-text-white is-size-7">Status</th>
                            <th class="has-text-white is-size-7">Descrição</th>
                            <th class="has-text-white is-size-7">Categoria</th>
                            <th class="has-text-white is-size-7">Fluxo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalEntradas = 0;
                        $totalSaidas = 0;

                        foreach ($transacoes as $transacao):
                            $valor = (float)$transacao['valor'];
                            if ($transacao['tipo'] === 'RECEBER') {
                                $totalEntradas += $valor;
                                $tagClass = 'is-success is-light';
                            } else {
                                $totalSaidas += $valor;
                                $tagClass = 'is-danger is-light';
                            }
                        ?>
                            <tr>
                                <td class="is-size-7"><?= esc($transacao['id']) ?></td>
                                <td>
                                    <span class="tag <?= $tagClass ?> is-small">
                                        <?= esc($transacao['tipo']) ?>
                                    </span>
                                </td>
                                <td class="is-size-7 has-text-right is-family-monospace">
                                    <?= number_format($valor, 2, ',', '.') ?>
                                </td>
                                <td class="is-size-7"><?= date('d/m/Y', strtotime($transacao['data_vencimento'])) ?></td>
                                <td class="is-size-7"><?= $transacao['data_caixa'] ? date('d/m/Y', strtotime($transacao['data_caixa'])) : '-' ?></td>
                                <td class="is-size-7"><?= esc($transacao['status']) ?></td>
                                <td class="is-size-7" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: normal;">
                                    <?= esc($transacao['descricao']) ?>
                                </td>
                                <td class="is-size-7"><?= esc($transacao['categoria_nome'] ?? '-') ?></td>
                                <td class="is-size-7"><?= esc($transacao['tipo_fluxo_categoria'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <tfoot>
                        <?php
                        // CÁLCULO FINAL CORRIGIDO:
                        $saldoDoPeriodo = $totalEntradas - $totalSaidas;
                        $saldoFinalTotal = $saldo_inicial + $saldoDoPeriodo;
                        ?>

                        <tr class="has-background-grey-lighter">
                            <td colspan="3" class="has-text-weight-bold">SALDO DO PERÍODO (Entradas - Saídas)</td>

                            <!-- Coluna 4-9 (5 colunas) -->
                            <td colspan="6">
                                <div class="level is-mobile is-narrow">
                                    <div class="level-left">
                                        <div class="mr-4 has-text-success-dark is-size-7">
                                            <strong>Entrada:</strong> R$ <?= number_format($totalEntradas, 2, ',', '.') ?>
                                        </div>
                                        <div class="has-text-danger-dark is-size-7">
                                            <strong>Saída:</strong> R$ <?= number_format($totalSaidas, 2, ',', '.') ?>
                                        </div>
                                    </div>
                                    <div class="level-right">
                                        <div class="is-size-6 has-text-weight-bold">
                                            SALDO FINAL DE CAIXA:
                                            <span class="<?= ($saldoFinalTotal >= 0) ? 'has-text-success-dark' : 'has-text-danger-dark' ?>">
                                                R$ <?= number_format($saldoFinalTotal, 2, ',', '.') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        <?php else: ?>

            <div class="notification is-warning is-light has-text-centered">
                <span class="icon is-large mb-2"><i class="fas fa-exclamation-triangle fa-2x"></i></span>
                <p>Nenhum registro de transação encontrado para o período de <strong><?= esc($periodo_txt) ?></strong>.</p>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php $this->endSection(); ?>
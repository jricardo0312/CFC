<?php
$this->extend('layout/principal');
$this->section('conteudo');
?>

<div class="columns is-centered">
    <div class="column is-10-widescreen is-12-desktop">

        <div class="level mb-5">
            <div class="level-left">
                <div>
                    <h1 class="title is-3 has-text-grey-darker mb-2">
                        <?= esc($titulo ?? 'Relatório DFC') ?>
                    </h1>
                    <p class="subtitle is-6 has-text-grey">
                        Demonstração dos Fluxos de Caixa (Consolidado)
                    </p>
                </div>
            </div>
            <div class="level-right">
                <button onclick="window.print()" class="button is-white is-outlined">
                    <span class="icon">
                        <i class="fas fa-print"></i>
                    </span>
                    <span>Imprimir / PDF</span>
                </button>
            </div>
        </div>

        <div class="box is-hidden-print has-background-white-ter">
            <h2 class="title is-6 is-uppercase has-text-grey-light mb-3">Filtrar Período</h2>

            <form action="<?= current_url() ?>" method="post">
                <?= csrf_field() ?>

                <div class="columns is-align-items-end">
                    <div class="column is-5-tablet">
                        <div class="field">
                            <label class="label">Data Início</label>
                            <div class="control has-icons-left">
                                <input class="input" type="date" name="data_inicio"
                                    value="<?= esc($data_inicio ?? date('Y-m-01')) ?>">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-calendar-day"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="column is-5-tablet">
                        <div class="field">
                            <label class="label">Data Fim</label>
                            <div class="control has-icons-left">
                                <input class="input" type="date" name="data_fim"
                                    value="<?= esc($data_fim ?? date('Y-m-t')) ?>">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-calendar-day"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="column is-2-tablet">
                        <div class="field">
                            <div class="control">
                                <button type="submit" class="button is-link is-fullwidth">
                                    <span class="icon is-small">
                                        <i class="fas fa-filter"></i>
                                    </span>
                                    <span>Filtrar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($dfc_resultados)) : ?>

            <div class="section has-text-centered is-dashed-border">
                <span class="icon is-large has-text-grey-light mb-3">
                    <i class="fas fa-search fa-3x"></i>
                </span>
                <p class="is-size-5 has-text-grey">Nenhum resultado para o período selecionado.</p>
            </div>

        <?php else: ?>

            <?php
            // --- PREPARAÇÃO DOS DADOS (Mantida idêntica) ---
            $fco = $dfc_resultados['FCO'] ?? ['entrada' => 0, 'saida' => 0, 'liquido' => 0];
            $fci = $dfc_resultados['FCI'] ?? ['entrada' => 0, 'saida' => 0, 'liquido' => 0];
            $fcf = $dfc_resultados['FCF'] ?? ['entrada' => 0, 'saida' => 0, 'liquido' => 0];

            $totalVariacao = $dfc_resultados['TOTAL'] ?? 0;
            $saldoAnterior = $dfc_resultados['SALDO_ANTERIOR'] ?? 0;
            $saldoFinal = $saldoAnterior + $totalVariacao;

            // Helper de cor para Bulma
            $getTextColor = fn($val) => $val < 0 ? 'has-text-danger' : 'has-text-success';
            ?>

            <div class="box p-0 overflow-hidden">
                <div class="table-container mb-0">
                    <table class="table is-fullwidth is-striped is-hoverable">
                        <thead>
                            <tr class="has-background-white-ter">
                                <th class="py-4 px-5">Descrição</th>
                                <th class="py-4 px-5 has-text-right">Entradas</th>
                                <th class="py-4 px-5 has-text-right">Saídas</th>
                                <th class="py-4 px-5 has-text-right has-background-grey-lighter">Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-5 py-4">
                                    <span class="has-text-weight-medium">Operacional</span>
                                    <span class="tag is-info is-light ml-2">FCO</span>
                                </td>
                                <td class="px-5 py-4 has-text-right"><?= number_format($fco['entrada'], 2, ',', '.') ?></td>
                                <td class="px-5 py-4 has-text-right"><?= number_format($fco['saida'], 2, ',', '.') ?></td>
                                <td class="px-5 py-4 has-text-right has-background-white-ter has-text-weight-bold <?= $getTextColor($fco['liquido']) ?>">
                                    <?= number_format($fco['liquido'], 2, ',', '.') ?>
                                </td>
                            </tr>

                            <tr>
                                <td class="px-5 py-4">
                                    <span class="has-text-weight-medium">Investimento</span>
                                    <span class="tag is-warning is-light ml-2">FCI</span>
                                </td>
                                <td class="px-5 py-4 has-text-right"><?= number_format($fci['entrada'], 2, ',', '.') ?></td>
                                <td class="px-5 py-4 has-text-right"><?= number_format($fci['saida'], 2, ',', '.') ?></td>
                                <td class="px-5 py-4 has-text-right has-background-white-ter has-text-weight-bold <?= $getTextColor($fci['liquido']) ?>">
                                    <?= number_format($fci['liquido'], 2, ',', '.') ?>
                                </td>
                            </tr>

                            <tr>
                                <td class="px-5 py-4">
                                    <span class="has-text-weight-medium">Financiamento</span>
                                    <span class="tag is-link is-light ml-2">FCF</span>
                                </td>
                                <td class="px-5 py-4 has-text-right"><?= number_format($fcf['entrada'], 2, ',', '.') ?></td>
                                <td class="px-5 py-4 has-text-right"><?= number_format($fcf['saida'], 2, ',', '.') ?></td>
                                <td class="px-5 py-4 has-text-right has-background-white-ter has-text-weight-bold <?= $getTextColor($fcf['liquido']) ?>">
                                    <?= number_format($fcf['liquido'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr style="border-top: 2px solid #dbdbdb;">
                                <td colspan="3" class="has-text-right is-uppercase is-size-7 has-text-weight-semibold has-text-grey">
                                    (=) Variação do Período
                                </td>
                                <td class="has-text-right is-size-5 has-text-weight-bold <?= $getTextColor($totalVariacao) ?>">
                                    <?= number_format($totalVariacao, 2, ',', '.') ?>
                                </td>
                            </tr>

                            <tr class="has-background-warning-light">
                                <td colspan="3" class="has-text-right is-uppercase is-size-7 has-text-weight-semibold has-text-grey-dark">
                                    (+) Saldo Anterior (em <?= date('d/m/Y', strtotime($data_inicio)) ?>)
                                </td>
                                <td class="has-text-right is-size-5 has-text-weight-bold has-text-grey-darker">
                                    <?= number_format($saldoAnterior, 2, ',', '.') ?>
                                </td>
                            </tr>

                            <tr class="has-background-grey-darker">
                                <td colspan="3" class="has-text-right is-uppercase has-text-weight-bold has-text-white">
                                    (=) Saldo Final Acumulado
                                </td>
                                <td class="has-text-right is-size-4 has-text-weight-bold has-text-white">
                                    R$ <?= number_format($saldoFinal, 2, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <p class="help has-text-right mt-2">
                * Saldo Final = Saldo Anterior + Variação do Período
            </p>

        <?php endif; ?>
    </div>
</div>

<?php $this->endSection(); ?>
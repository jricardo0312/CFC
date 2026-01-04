<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');
?>

<section class="section">
    <div class="container">
        <h1 class="title">Relatório de Transações por Tipo de Cliente</h1>

        <!-- Formulário de Filtro -->
        <div class="box">
            <form method="get" action="">
                <div class="columns is-multiline">
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Data Início</label>
                            <div class="control">
                                <input type="date" name="data_inicio" class="input"
                                    value="<?= $filtros['data_inicio'] ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Data Fim</label>
                            <div class="control">
                                <input type="date" name="data_fim" class="input"
                                    value="<?= $filtros['data_fim'] ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Tipo de Cliente</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="tipo_cliente" class="input">
                                        <option value="todos">Todos os Clientes</option>
                                        <?php foreach ($tiposCliente as $tipo): ?>
                                            <option value="<?= $tipo['tipo_cliente'] ?>"
                                                <?= ($filtros['tipo_cliente'] ?? '') == $tipo['tipo_cliente'] ? 'selected' : '' ?>>
                                                <?= ucfirst($tipo['tipo_cliente']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Tipo de Transação</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="tipo_transacao" class="input">
                                        <option value="todos">Todas</option>
                                        <option value="RECEBER" <?= ($filtros['tipo_transacao'] ?? '') == 'RECEBER' ? 'selected' : '' ?>>A Receber</option>
                                        <option value="PAGAR" <?= ($filtros['tipo_transacao'] ?? '') == 'PAGAR' ? 'selected' : '' ?>>A Pagar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="column is-full">
                        <div class="field is-grouped">
                            <div class="control">
                                <button type="submit" class="button is-primary">
                                    <i class="fas fa-filter"></i> &nbsp; Filtrar
                                </button>
                                <a href="?" class="button is-light">Limpar Filtros</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Botão Exportar -->
        <div class="mb-4">
            <a href="<?= base_url('relatorio/tipocliente/exportar') . '?' . http_build_query($filtros) ?>"
                class="button is-success" target="_blank">
                <i class="fas fa-file-export"></i> &nbsp; Exportar CSV
            </a>
        </div>

        <!-- Resumo Geral -->
        <?php if (!empty($transacoes)): ?>
            <div class="notification is-info is-light">
                <h3 class="subtitle is-5">Resumo Geral</h3>
                <div class="columns is-multiline">
                    <div class="column is-one-quarter">
                        <div class="box">
                            <p class="title is-4 has-text-success">R$ <?= number_format($resumoGeral['total_receber'] ?? 0, 2, ',', '.') ?></p>
                            <p class="subtitle is-6">Total a Receber</p>
                        </div>
                    </div>
                    <div class="column is-one-quarter">
                        <div class="box">
                            <p class="title is-4 has-text-danger">R$ <?= number_format($resumoGeral['total_pagar'] ?? 0, 2, ',', '.') ?></p>
                            <p class="subtitle is-6">Total a Pagar</p>
                        </div>
                    </div>
                    <div class="column is-one-quarter">
                        <div class="box">
                            <?php
                            $saldo = ($resumoGeral['saldo'] ?? 0);
                            $classeSaldo = $saldo >= 0 ? 'saldo-positivo' : 'saldo-negativo';
                            ?>
                            <p class="title is-4 <?= $classeSaldo ?>">R$ <?= number_format(abs($saldo), 2, ',', '.') ?></p>
                            <p class="subtitle is-6"><?= $saldo >= 0 ? 'Saldo Positivo' : 'Saldo Negativo' ?></p>
                        </div>
                    </div>
                    <div class="column is-one-quarter">
                        <div class="box">
                            <p class="title is-4"><?= $resumoGeral['quantidade_total'] ?? 0 ?></p>
                            <p class="subtitle is-6">Total de Transações</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Resumo por Tipo de Cliente -->
        <?php if (!empty($totais)): ?>
            <div class="notification is-light">
                <h3 class="subtitle is-5">Resumo por Tipo de Cliente</h3>
                <div class="columns is-multiline">
                    <?php foreach ($totais as $total): ?>
                        <div class="column is-one-third">
                            <div class="box">
                                <p class="title is-4 <?= $total['tipo'] == 'RECEBER' ? 'has-text-success' : 'has-text-danger' ?>">
                                    R$ <?= number_format($total['total'], 2, ',', '.') ?>
                                </p>
                                <p class="subtitle is-6">
                                    <?= ucfirst($total['tipo_cliente']) ?> -
                                    <span class="tag <?= $total['tipo'] == 'RECEBER' ? 'tag-receber' : 'tag-pagar' ?>">
                                        <?= $total['tipo'] == 'RECEBER' ? 'A Receber' : 'A Pagar' ?>
                                    </span>
                                </p>
                                <p class="is-size-7"><?= $total['quantidade'] ?> transações</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabela de Transações -->
        <div class="box">
            <h2 class="subtitle">Transações Detalhadas</h2>

            <?php if (empty($transacoes)): ?>
                <div class="notification is-warning">
                    Nenhuma transação encontrada com os filtros aplicados.
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="table is-striped is-fullwidth is-hoverable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Tipo Cliente</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalGeral = 0;
                            foreach ($transacoes as $transacao):
                                $totalGeral += ($transacao['tipo'] == 'RECEBER' ? $transacao['valor'] : -$transacao['valor']);
                            ?>
                                <tr>
                                    <td><?= $transacao['id'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($transacao['data_caixa'])) ?></td>
                                    <td><?= htmlspecialchars($transacao['nome']) ?></td>
                                    <td>
                                        <span class="tag is-info">
                                            <?= ucfirst($transacao['tipo_cliente']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="tag <?= $transacao['tipo'] == 'RECEBER' ? 'tag-receber' : 'tag-pagar' ?>">
                                            <?= $transacao['tipo'] == 'RECEBER' ? 'A Receber' : 'A Pagar' ?>
                                        </span>
                                    </td>
                                    <td class="<?= $transacao['tipo'] == 'RECEBER' ? 'has-text-success' : 'has-text-danger' ?>">
                                        R$ <?= number_format($transacao['valor'], 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <a href="#" class="button is-small is-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="has-text-right">Saldo Final:</th>
                                <th colspan="2" class="<?= $totalGeral >= 0 ? 'has-text-success' : 'has-text-danger' ?>">
                                    R$ <?= number_format(abs($totalGeral), 2, ',', '.') ?>
                                    <?= $totalGeral >= 0 ? '(Positivo)' : '(Negativo)' ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    // Adicionar confirmação ao exportar
    document.querySelectorAll('a[href*="exportar"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Deseja exportar os dados?')) {
                e.preventDefault();
            }
        });
    });

    // Formatar datas automaticamente
    document.addEventListener('DOMContentLoaded', function() {
        const hoje = new Date().toISOString().split('T')[0];
        const primeiroDiaMes = new Date(new Date().getFullYear(), new Date().getMonth(), 2).toISOString().split('T')[0];

        // Preencher datas se estiverem vazias
        if (!document.querySelector('input[name="data_inicio"]').value) {
            document.querySelector('input[name="data_inicio"]').value = primeiroDiaMes;
        }
        if (!document.querySelector('input[name="data_fim"]').value) {
            document.querySelector('input[name="data_fim"]').value = hoje;
        }
    });
</script>
<?php $this->endSection(); ?>
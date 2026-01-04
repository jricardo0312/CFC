<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Transações por Tipo de Cliente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .tag-receber {
            background-color: #48c774;
            color: white;
        }

        .tag-pagar {
            background-color: #f14668;
            color: white;
        }

        .saldo-positivo {
            color: #48c774;
            font-weight: bold;
        }

        .saldo-negativo {
            color: #f14668;
            font-weight: bold;
        }

        .tag-status-concluida {
            background-color: #48c774;
            color: white;
        }

        .tag-status-pendente {
            background-color: #ffdd57;
            color: rgba(0, 0, 0, .7);
        }
    </style>
</head>

<body>
    <section class="section">
        <div class="container">
            <h1 class="title">Relatório de Transações por Tipo de Cliente</h1>

            <!-- Debug info (pode remover depois) -->
            <div class="notification is-info is-light">
                <p><strong>Informações:</strong> <?= $contagem['transacoes'] ?> transações encontradas |
                    <?= $contagem['tipos_cliente'] ?> tipos de cliente |
                    <?= $contagem['tipos_transacao'] ?> tipos de transação</p>
            </div>

            <!-- Formulário de Filtro -->
            <div class="box">
                <form method="get" action="">
                    <div class="columns is-multiline">
                        <div class="column is-one-quarter">
                            <div class="field">
                                <label class="label">Data Início</label>
                                <div class="control">
                                    <input type="date" name="data_inicio" class="input"
                                        value="<?= $filtros['data_inicio'] ?? date('Y-m-01') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="column is-one-quarter">
                            <div class="field">
                                <label class="label">Data Fim</label>
                                <div class="control">
                                    <input type="date" name="data_fim" class="input"
                                        value="<?= $filtros['data_fim'] ?? date('Y-m-d') ?>">
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
                                            <?php if (!empty($tiposCliente)): ?>
                                                <?php foreach ($tiposCliente as $tipo): ?>
                                                    <option value="<?= $tipo['tipo_cliente'] ?>"
                                                        <?= ($filtros['tipo_cliente'] ?? '') == $tipo['tipo_cliente'] ? 'selected' : '' ?>>
                                                        <?= ucfirst($tipo['tipo_cliente']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
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
                                    <a href="<?= site_url('relatorio/tipocliente/teste') ?>" class="button is-info is-light">
                                        <i class="fas fa-vial"></i> &nbsp; Testar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Botão Exportar -->
            <div class="mb-4">
                <a href="<?= site_url('relatorio/tipocliente/exportar') . '?' . http_build_query($filtros) ?>"
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
                                <p class="title is-4 has-text-success">R$ <?= number_format($resumoGeral['total_receber'], 2, ',', '.') ?></p>
                                <p class="subtitle is-6">Total a Receber</p>
                            </div>
                        </div>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <p class="title is-4 has-text-danger">R$ <?= number_format($resumoGeral['total_pagar'], 2, ',', '.') ?></p>
                                <p class="subtitle is-6">Total a Pagar</p>
                            </div>
                        </div>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <?php
                                $saldo = $resumoGeral['saldo'];
                                $classeSaldo = $saldo >= 0 ? 'saldo-positivo' : 'saldo-negativo';
                                ?>
                                <p class="title is-4 <?= $classeSaldo ?>">R$ <?= number_format(abs($saldo), 2, ',', '.') ?></p>
                                <p class="subtitle is-6"><?= $saldo >= 0 ? 'Saldo Positivo' : 'Saldo Negativo' ?></p>
                            </div>
                        </div>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <p class="title is-4"><?= $resumoGeral['quantidade_total'] ?></p>
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
                <h2 class="subtitle">Transações Detalhadas (<?= count($transacoes) ?>)</h2>

                <?php if (empty($transacoes)): ?>
                    <div class="notification is-warning">
                        Nenhuma transação encontrada com os filtros aplicados.
                        <br><br>
                        <a href="?" class="button is-small">Tentar sem filtros</a>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table is-striped is-fullwidth is-hoverable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data Caixa</th>
                                    <th>Cliente</th>
                                    <th>Tipo Cliente</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalGeral = 0;
                                foreach ($transacoes as $transacao):
                                    $valorTransacao = $transacao['valor'];
                                    $tipoTransacao = $transacao['tipo'];
                                    $totalGeral += ($tipoTransacao == 'RECEBER' ? $valorTransacao : -$valorTransacao);
                                ?>
                                    <tr>
                                        <td><?= $transacao['id'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($transacao['data_caixa'])) ?></td>
                                        <td><?= htmlspecialchars($transacao['nome']) ?></td>
                                        <td>
                                            <span class="tag is-info">
                                                <?= ucfirst($transacao['tipo_cliente']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="tag <?= $tipoTransacao == 'RECEBER' ? 'tag-receber' : 'tag-pagar' ?>">
                                                <?= $tipoTransacao == 'RECEBER' ? 'A Receber' : 'A Pagar' ?>
                                            </span>
                                        </td>
                                        <td class="<?= $tipoTransacao == 'RECEBER' ? 'has-text-success' : 'has-text-danger' ?>">
                                            R$ <?= number_format($valorTransacao, 2, ',', '.') ?>
                                        </td>
                                        <td>
                                            <span class="tag <?= $transacao['status'] == 'CONCLUIDA' ? 'tag-status-concluida' : 'tag-status-pendente' ?>">
                                                <?= $transacao['status'] ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars(substr($transacao['descricao'], 0, 50)) ?><?= strlen($transacao['descricao']) > 50 ? '...' : '' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="has-text-right">Saldo Final:</th>
                                    <th colspan="3" class="<?= $totalGeral >= 0 ? 'has-text-success' : 'has-text-danger' ?>">
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
    </script>
</body>

</html>
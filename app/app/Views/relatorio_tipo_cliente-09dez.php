<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');
?>

<div class="container">
    <h1 class="title">Relatório de Transações por Tipo de Cliente</h1>

    <!-- Formulário de Filtro -->
    <div class="box">
        <form method="get" action="">
            <div class="columns">
                <div class="column">
                    <div class="field">
                        <label class="label">Data Início</label>
                        <div class="control">
                            <input type="date" name="data_inicio" class="input"
                                value="<?= $filtros['data_inicio'] ?? '' ?>">
                        </div>
                    </div>
                </div>

                <div class="column">
                    <div class="field">
                        <label class="label">Data Fim</label>
                        <div class="control">
                            <input type="date" name="data_fim" class="input"
                                value="<?= $filtros['data_fim'] ?? '' ?>">
                        </div>
                    </div>
                </div>

                <div class="column">
                    <div class="field">
                        <label class="label">Tipo de Cliente</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="tipo_cliente" class="input">
                                    <option value="todos">Todos os Tipos</option>
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

                <div class="column is-narrow">
                    <div class="field">
                        <label class="label">&nbsp;</label>
                        <div class="control">
                            <button type="submit" class="button is-primary">
                                <i class="fas fa-filter"></i> &nbsp; Filtrar
                            </button>
                            <a href="?" class="button is-light">Limpar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Botão Exportar -->
    <div class="mb-4">
        <a href="<?= base_url('relatorio/exportar') . '?' . http_build_query($filtros) ?>"
            class="button is-success">
            <i class="fas fa-file-export"></i> &nbsp; Exportar CSV
        </a>
    </div>

    <!-- Resumo -->
    <?php if (!empty($totais)): ?>
        <div class="notification is-info is-light">
            <h3 class="subtitle is-5">Resumo por Tipo de Cliente</h3>
            <div class="columns is-multiline">
                <?php foreach ($totais as $total): ?>
                    <div class="column is-one-third">
                        <div class="box">
                            <p class="title is-4">R$ <?= number_format($total['total'], 2, ',', '.') ?></p>
                            <p class="subtitle is-6"><?= ucfirst($total['tipo_cliente']) ?></p>
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
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalGeral = 0;
                        foreach ($transacoes as $transacao):
                            $totalGeral += $transacao['valor'];
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
                                <td>R$ <?= number_format($transacao['valor'], 2, ',', '.') ?></td>
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
                            <th colspan="4" class="has-text-right">Total Geral:</th>
                            <th colspan="2">R$ <?= number_format($totalGeral, 2, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>


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

<?php $this->endSection(); ?>
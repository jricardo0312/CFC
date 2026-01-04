<!-- app/Views/relatorio_tipo_cliente.php -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Debug</title>
    <style>
        .debug {
            background: #f0f0f0;
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #333;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .warning {
            color: orange;
        }
    </style>
</head>

<body>
    <h1>Relatório de Transações - DEBUG</h1>

    <div class="debug">
        <h2>Informações de Debug:</h2>

        <h3>Filtros Aplicados:</h3>
        <pre><?php print_r($debug_filtros ?? []); ?></pre>

        <h3>Estatísticas:</h3>
        <ul>
            <li>Total de transações: <strong><?= $debug_info['total_transacoes'] ?? 0 ?></strong></li>
            <li>Tipos de cliente encontrados: <strong><?= $debug_info['total_tipos_cliente'] ?? 0 ?></strong></li>
            <li>Coluna de relacionamento: <strong><?= $debug_info['coluna_relacionamento'] ?? 'Não detectada' ?></strong></li>
        </ul>

        <h3>Dados Brutos (primeiros 5 registros):</h3>
        <?php if (!empty($transacoes)): ?>
            <pre><?php print_r(array_slice($transacoes, 0, 5)); ?></pre>
        <?php else: ?>
            <p class="warning">Nenhuma transação encontrada</p>
        <?php endif; ?>

        <h3>Links para Diagnóstico:</h3>
        <ul>
            <li><a href="<?= site_url('relatorio/debug') ?>" target="_blank">Debug Completo</a></li>
            <li><a href="<?= site_url('relatorio/testar-colunas') ?>" target="_blank">Testar Colunas</a></li>
            <li><a href="<?= site_url('relatorio/tipocliente?showdata=1') ?>">Ver Todos os Dados</a></li>
        </ul>
    </div>

    <!-- Formulário de Filtro Simples -->
    <form method="get" action="">
        <h3>Filtros:</h3>
        Data Início: <input type="date" name="data_inicio" value="<?= $filtros['data_inicio'] ?? '' ?>"><br>
        Data Fim: <input type="date" name="data_fim" value="<?= $filtros['data_fim'] ?? '' ?>"><br>
        <button type="submit">Filtrar</button>
        <a href="?">Limpar</a>
    </form>

    <hr>

    <?php if (!empty($transacoes)): ?>
        <h2>Transações (<?= count($transacoes) ?>)</h2>
        <table border="1" cellpadding="5">
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Tipo Cliente</th>
                <th>Tipo</th>
                <th>Valor</th>
            </tr>
            <?php foreach ($transacoes as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= $t['data_caixa'] ?? $t['created_at'] ?? '' ?></td>
                    <td><?= $t['nome'] ?? '' ?></td>
                    <td><?= $t['tipo_cliente'] ?? '' ?></td>
                    <td><?= $t['tipo'] ?? '' ?></td>
                    <td>R$ <?= number_format($t['valor'] ?? 0, 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <h2 class="error">Nenhuma transação encontrada!</h2>
        <p>Possíveis causas:</p>
        <ol>
            <li>Não há dados nas tabelas</li>
            <li>A coluna de relacionamento está incorreta</li>
            <li>Os filtros estão muito restritivos</li>
            <li>Problema na consulta SQL</li>
        </ol>
    <?php endif; ?>
</body>

</html>
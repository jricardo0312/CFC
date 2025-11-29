<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <!-- Estilos básicos para o relatório -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-panel {
            background: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            align-items: flex-end;
        }

        .form-group {
            flex: 1 1 200px;
            min-width: 180px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .btn {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-group {
            flex: 1 1 100%;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            cursor: pointer;
            transition: background-color 0.3s;
            width: auto;
            min-width: 100px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #1e7e34;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #117a8b;
        }

        .table-responsive {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        .total-row td {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .badge-entrada {
            background-color: #d4edda;
            color: #155724;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        .badge-saida {
            background-color: #f8d7da;
            color: #721c24;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        /* Estilos específicos para impressão */
        @media print {
            body {
                background-color: white;
            }

            .container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }

            .form-panel,
            .btn-group,
            .actions-row {
                display: none;
            }

            table {
                border: 1px solid #000;
            }

            table th,
            table td {
                border: 1px solid #000;
            }

            h1 {
                color: #000;
                border-bottom: 1px solid #000;
            }

            .print-header {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1><?= esc($title) ?></h1>

        <!-- Painel de Filtro de Período -->
        <?= form_open(site_url('relatorio'), ['method' => 'get', 'class' => 'form-panel']) ?>
        <div class="form-group">
            <label for="data_inicio">Data de Início (Caixa):</label>
            <input type="date" id="data_inicio" name="data_inicio" value="<?= esc($data_inicio) ?>" required>
        </div>

        <div class="form-group">
            <label for="data_fim">Data de Fim (Caixa):</label>
            <input type="date" id="data_fim" name="data_fim" value="<?= esc($data_fim) ?>" required>
        </div>

        <div class="form-group" style="flex: 0 1 auto;">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
        <?= form_close() ?>

        <!-- Linha de Ações (Impressão e Exportação) -->
        <div class="btn-group actions-row">
            <button onclick="window.print()" class="btn btn-info">Imprimir</button>

            <!-- Formulário para Exportação CSV (usa as mesmas datas do filtro) -->
            <?= form_open(site_url('relatorio/exportarCsv'), ['method' => 'get']) ?>
            <input type="hidden" name="data_inicio" value="<?= esc($data_inicio) ?>">
            <input type="hidden" name="data_fim" value="<?= esc($data_fim) ?>">
            <button type="submit" class="btn btn-success">Exportar CSV</button>
            <?= form_close() ?>
        </div>

        <!-- Cabeçalho de Impressão -->
        <div class="print-header" style="display: none;">
            <h2>Relatório de Transações</h2>
            <p>Período: <?= esc($periodo_txt) ?></p>
        </div>

        <?php if (!empty($transacoes)): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Data Caixa (DFC)</th>
                            <th>Status</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <!-- <th>Pessoa</th> -->
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
                                $badgeClass = 'badge-entrada';
                            } else {
                                $totalSaidas += $valor;
                                $badgeClass = 'badge-saida';
                            }
                        ?>
                            <tr>
                                <td><?= esc($transacao['id']) ?></td>
                                <td><span class="<?= $badgeClass ?>"><?= esc($transacao['tipo']) ?></span></td>
                                <td style="text-align: right;">R$ <?= number_format($valor, 2, ',', '.') ?></td>
                                <td><?= date('d/m/Y', strtotime($transacao['data_vencimento'])) ?></td>
                                <td><?= $transacao['data_caixa'] ? date('d/m/Y', strtotime($transacao['data_caixa'])) : 'N/A' ?></td>
                                <td><?= esc($transacao['status']) ?></td>
                                <td><?= esc($transacao['descricao']) ?></td>
                                <td><?= esc($transacao['categoria_nome'] ?? 'N/A') ?></td>
                                <!-- <td><?= esc($transacao['pessoa_nome'] ?? 'N/A') ?></td> -->
                            </tr>
                        <?php endforeach; ?>

                        <!-- Linha de Totais -->
                        <tr class="total-row">
                            <td colspan="2">Totais do Período</td>

                            <td style="text-align: right;">
                            <td colspan="2">
                                <p style="margin: 0; color: #28a745;">Entradas: R$ <?= number_format($totalEntradas, 2, ',', '.') ?></p>
                                <p style="margin: 0; color: #dc3545;">Saídas: R$ <?= number_format($totalSaidas, 2, ',', '.') ?></p>
                            </td>
                            </td>
                            <td colspan="4">
                                Saldo Final:
                                <span style="color: <?= ($totalEntradas - $totalSaidas) >= 0 ? '#28a745' : '#dc3545' ?>;">
                                    R$ <?= number_format($totalEntradas - $totalSaidas, 2, ',', '.') ?>
                                </span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #dc3545;">Nenhum registro de transação encontrado para o período de <b><?= esc($periodo_txt) ?></b>.</p>
        <?php endif; ?>
    </div>

</body>

</html>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
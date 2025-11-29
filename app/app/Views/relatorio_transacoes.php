<?php
// Define o layout principal (presume-se que 'layout/principal' carrega o Tailwind CDN)
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');
?>

<!-- Estilos para Otimizar a Impressão -->
<style>
    /* Estilos específicos para impressão para garantir que tudo caiba na folha */
    @media print {

        /* Garante que o contêiner não tenha margens desnecessárias na impressão */
        .container-print-optimized {
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            max-width: 100% !important;
        }

        /* Reduz o tamanho da fonte para compactar o conteúdo */
        table,
        table th,
        table td,
        .total-row {
            font-size: 8pt !important;
        }

        /* Reduz o padding nas células para maximizar o espaço horizontal */
        .px-2 {
            padding-left: 0.2rem !important;
            padding-right: 0.2rem !important;
        }

        /* Força quebra de linha em todas as células (exceto nos botões que já estão hidden) */
        .whitespace-nowrap {
            white-space: normal !important;
        }

        /* Otimização da linha de totais para caber no final */
        .text-right {
            text-align: right !important;
        }

        .text-lg {
            font-size: 10pt !important;
        }
    }
</style>

<!-- Alterado para max-w-full para ocupar toda a largura disponível. Adicionando classe de otimização de impressão. -->
<div class="max-w-full mx-auto p-4 sm:p-6 lg:p-8 bg-white shadow-2xl rounded-xl my-8 container-print-optimized">
    <h1 class="text-3xl font-extrabold text-blue-700 border-b-4 border-blue-500 pb-3 mb-6 text-center uppercase">
        <?= esc($title) ?>
    </h1>

    <!-- Painel de Filtro de Período -->
    <?= form_open(site_url('relatorio'), ['method' => 'get', 'class' => 'bg-gray-50 p-4 sm:p-6 rounded-lg shadow-inner mb-6 flex flex-wrap gap-4 items-end justify-center print:hidden']) ?>
    <div class="flex-1 min-w-[160px] max-w-xs">
        <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-1">Data de Início (Caixa):</label>
        <input type="date" id="data_inicio" name="data_inicio" value="<?= esc($data_inicio) ?>" required
            class="w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150">
    </div>

    <div class="flex-1 min-w-[160px] max-w-xs">
        <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-1">Data de Fim (Caixa):</label>
        <input type="date" id="data_fim" name="data_fim" value="<?= esc($data_fim) ?>" required
            class="w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150">
    </div>

    <div class="min-w-[100px] flex-grow-0">
        <button type="submit" class="w-full p-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
            Filtrar
        </button>
    </div>
    <?= form_close() ?>

    <!-- Linha de Ações (Impressão e Exportação) -->
    <div class="flex justify-end gap-3 mt-4 print:hidden">
        <button onclick="window.print()" class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-200 min-w-[100px]">
            Imprimir
        </button>

        <!-- Formulário para Exportação CSV (usa as mesmas datas do filtro) -->
        <?= form_open(site_url('relatorio/exportarCsv'), ['method' => 'get']) ?>
        <input type="hidden" name="data_inicio" value="<?= esc($data_inicio) ?>">
        <input type="hidden" name="data_fim" value="<?= esc($data_fim) ?>">
        <button type="submit" class="p-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition duration-200 min-w-[100px]">
            Exportar CSV
        </button>
        <?= form_close() ?>
    </div>

    <!-- Cabeçalho de Impressão (Exibido apenas na impressão) -->
    <div class="hidden print:block text-center mb-6">
        <h2 class="text-xl font-bold">Relatório de Transações</h2>
        <p class="text-sm">Período: <?= esc($periodo_txt) ?></p>
    </div>

    <?php if (!empty($transacoes)): ?>
        <!-- overflow-x-auto é necessário para telas pequenas, mas agora tentamos evitar o scroll forçado -->
        <div class="overflow-x-auto shadow-lg rounded-lg mt-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <!-- Reduzindo padding horizontal para px-2 -->
                        <th class="px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                        <th class="px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tipo</th>
                        <th class="px-2 py-3 text-right text-xs font-semibold uppercase tracking-wider">Valor</th>
                        <th class="px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider">Vencimento</th>
                        <th class="px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider">Data Caixa (DFC)</th>
                        <th class="px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider">Descrição</th>
                        <th class="px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider">Categoria</th>
                        <th class="px-2 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tipo de Fluxo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $totalEntradas = 0;
                    $totalSaidas = 0;
                    foreach ($transacoes as $transacao):
                        $valor = (float)$transacao['valor'];
                        if ($transacao['tipo'] === 'RECEBER') {
                            $totalEntradas += $valor;
                            $badgeClass = 'bg-green-100 text-green-800';
                        } else {
                            $totalSaidas += $valor;
                            $badgeClass = 'bg-red-100 text-red-800';
                        }
                    ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900"><?= esc($transacao['id']) ?></td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <span class="<?= $badgeClass ?> px-2 py-0.5 text-xs font-semibold rounded-full">
                                    <?= esc($transacao['tipo']) ?>
                                </span>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap text-sm font-mono text-right text-gray-800">R$ <?= number_format($valor, 2, ',', '.') ?></td>
                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500"><?= date('d/m/Y', strtotime($transacao['data_vencimento'])) ?></td>
                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500"><?= $transacao['data_caixa'] ? date('d/m/Y', strtotime($transacao['data_caixa'])) : 'N/A' ?></td>
                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500"><?= esc($transacao['status']) ?></td>
                            <!-- Forçando quebra de linha com break-words -->
                            <td class="px-2 py-3 max-w-xs text-sm text-gray-900 break-words"><?= esc($transacao['descricao']) ?></td>
                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900"><?= esc($transacao['categoria_nome'] ?? 'N/A') ?></td>
                            <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900"><?= esc($transacao['tipo_fluxo_categoria'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Linha de Totais -->
                    <tr class="font-bold bg-gray-200 total-row">
                        <td colspan="2" class="px-2 py-3">Totais do Período</td>

                        <!-- Coluna 3: Valor (Exibe Entradas/Saídas) -->
                        <td class="px-2 py-3 text-left">
                            <div class="text-sm">
                                <p class="text-green-600">Entrada: R$ <?= number_format($totalEntradas, 2, ',', '.') ?></p>
                                <p class="text-red-600">Saída: R$ <?= number_format($totalSaidas, 2, ',', '.') ?></p>
                            </div>
                        </td>

                        <!-- Coluna 4-9: Resto (Exibe Saldo Final). Colspan ajustado para 6 colunas. -->
                        <td colspan="6" class="px-2 py-3 text-right text-lg">
                            Saldo Final:
                            <span class="<?= ($totalEntradas - $totalSaidas) >= 0 ? 'text-green-700' : 'text-red-700' ?>">
                                R$ <?= number_format($totalEntradas - $totalSaidas, 2, ',', '.') ?>
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center p-8 text-xl font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg">
            Nenhum registro de transação encontrado para o período de <b><?= esc($periodo_txt) ?></b>.
        </p>
    <?php endif; ?>
</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
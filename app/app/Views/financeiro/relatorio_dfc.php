<?php
$this->extend('layout/principal');
$this->section('conteudo');
?>

<div class="container mx-auto px-4 py-8 max-w-5xl">

    <div class="mb-8 flex flex-col md:flex-row justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">
            <?= esc($titulo ?? 'Relatório DFC') ?>
        </h1>
        <button onclick="window.print()" class="text-gray-600 hover:text-gray-900 transition-colors flex items-center gap-2 text-sm border border-gray-300 px-3 py-1 rounded-md hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Imprimir / PDF
        </button>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-8 border border-gray-100 print:hidden">
        <h2 class="text-gray-500 text-sm uppercase font-semibold mb-4 tracking-wide">Filtrar Período</h2>
        <form class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end" method="post" action="<?= current_url() ?>">
            <?= csrf_field() ?>
            <div class="md:col-span-5">
                <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?= esc($data_inicio ?? date('Y-m-01')) ?>" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
            </div>
            <div class="md:col-span-5">
                <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                <input type="date" id="data_fim" name="data_fim" value="<?= esc($data_fim ?? date('Y-m-t')) ?>" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow transition-colors duration-200">Filtrar</button>
            </div>
        </form>
    </div>

    <?php if (empty($dfc_resultados)) : ?>
        <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
            <p class="text-gray-500 text-lg">Nenhum resultado para o período selecionado.</p>
        </div>
    <?php else: ?>

        <?php
        // --- PREPARAÇÃO DOS DADOS ---
        $fco = $dfc_resultados['FCO'] ?? ['entrada' => 0, 'saida' => 0, 'liquido' => 0];
        $fci = $dfc_resultados['FCI'] ?? ['entrada' => 0, 'saida' => 0, 'liquido' => 0];
        $fcf = $dfc_resultados['FCF'] ?? ['entrada' => 0, 'saida' => 0, 'liquido' => 0];

        $totalVariacao = $dfc_resultados['TOTAL'] ?? 0;

        // Novo dado vindo do controller
        $saldoAnterior = $dfc_resultados['SALDO_ANTERIOR'] ?? 0;

        // Cálculo final
        $saldoFinal = $saldoAnterior + $totalVariacao;

        // Helper de cor
        $getColor = fn($val) => $val < 0 ? 'text-red-600' : 'text-green-600';
        ?>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs leading-normal">
                            <th class="py-3 px-6 text-left font-semibold">Descrição</th>
                            <th class="py-3 px-6 text-right font-semibold">Entradas</th>
                            <th class="py-3 px-6 text-right font-semibold">Saídas</th>
                            <th class="py-3 px-6 text-right font-bold text-gray-700 bg-gray-100">Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">

                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-4 px-6 text-left"><span class="font-medium">Operacional</span> <span class="ml-1 px-2 text-[10px] font-semibold rounded-full bg-blue-100 text-blue-800">FCO</span></td>
                            <td class="py-4 px-6 text-right"><?= number_format($fco['entrada'], 2, ',', '.') ?></td>
                            <td class="py-4 px-6 text-right"><?= number_format($fco['saida'], 2, ',', '.') ?></td>
                            <td class="py-4 px-6 text-right bg-gray-50 font-bold <?= $getColor($fco['liquido']) ?>"><?= number_format($fco['liquido'], 2, ',', '.') ?></td>
                        </tr>

                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-4 px-6 text-left"><span class="font-medium">Investimento</span> <span class="ml-1 px-2 text-[10px] font-semibold rounded-full bg-yellow-100 text-yellow-800">FCI</span></td>
                            <td class="py-4 px-6 text-right"><?= number_format($fci['entrada'], 2, ',', '.') ?></td>
                            <td class="py-4 px-6 text-right"><?= number_format($fci['saida'], 2, ',', '.') ?></td>
                            <td class="py-4 px-6 text-right bg-gray-50 font-bold <?= $getColor($fci['liquido']) ?>"><?= number_format($fci['liquido'], 2, ',', '.') ?></td>
                        </tr>

                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-4 px-6 text-left"><span class="font-medium">Financiamento</span> <span class="ml-1 px-2 text-[10px] font-semibold rounded-full bg-purple-100 text-purple-800">FCF</span></td>
                            <td class="py-4 px-6 text-right"><?= number_format($fcf['entrada'], 2, ',', '.') ?></td>
                            <td class="py-4 px-6 text-right"><?= number_format($fcf['saida'], 2, ',', '.') ?></td>
                            <td class="py-4 px-6 text-right bg-gray-50 font-bold <?= $getColor($fcf['liquido']) ?>"><?= number_format($fcf['liquido'], 2, ',', '.') ?></td>
                        </tr>
                    </tbody>

                    <tfoot class="bg-gray-50">

                        <tr class="border-t-2 border-gray-300">
                            <td colspan="3" class="py-3 px-6 text-right text-gray-600 font-semibold uppercase text-xs tracking-wider">
                                (=) Variação do Período
                            </td>
                            <td class="py-3 px-6 text-right text-base font-bold <?= $getColor($totalVariacao) ?>">
                                <?= number_format($totalVariacao, 2, ',', '.') ?>
                            </td>
                        </tr>

                        <tr class="border-t border-gray-200 bg-yellow-50">
                            <td colspan="3" class="py-3 px-6 text-right text-yellow-800 font-semibold uppercase text-xs tracking-wider">
                                (+) Saldo Anterior (em <?= date('d/m/Y', strtotime($data_inicio)) ?>)
                            </td>
                            <td class="py-3 px-6 text-right text-base font-bold text-yellow-700">
                                <?= number_format($saldoAnterior, 2, ',', '.') ?>
                            </td>
                        </tr>

                        <tr class="bg-gray-500 text-white border-t-2 border-gray-600">
                            <td colspan="3" class="py-4 px-6 text-right font-bold uppercase tracking-wider">
                                (=) Saldo Final Acumulado
                            </td>
                            <td class="py-4 px-6 text-right text-lg font-extrabold">
                                R$ <?= number_format($saldoFinal, 2, ',', '.') ?>
                            </td>
                        </tr>

                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-2 text-right text-xs text-gray-400">
            * Saldo Final = Saldo Anterior + Variação do Período
        </div>

    <?php endif; ?>
</div>

<?php $this->endSection(); ?>
<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Demonstração dos Fluxos de Caixa (DFC)</h1>
    <p class="mb-6 text-sm text-gray-600">Relatório baseado no **Método Direto**. Inclui apenas transações com status "CONCLUÍDA" (movimento de caixa real).</p>

    <!-- Área de Flash Messages -->
    <?php if (session()->getFlashdata('erro')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?= esc(session()->getFlashdata('erro')) ?>
        </div>
    <?php endif; ?>

    <!-- Formulário de Filtro de Período -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Filtrar Período de Caixa</h2>

        <!-- O formulário aponta para a mesma rota, mas o método POST aciona o cálculo no Controller -->
        <?= form_open(route_to('relatorio_dfc'), ['class' => 'flex flex-col md:flex-row items-center space-y-3 md:space-y-0 md:space-x-4']) ?>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-1" for="data_inicio">Data Inicial</label>
            <!-- Usamos os valores passados pelo Controller (ou os valores padrões/submetidos) -->
            <input type="date" name="data_inicio" id="data_inicio" value="<?= esc($data_inicio) ?>" required class="shadow appearance-none border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-bold mb-1" for="data_fim">Data Final</label>
            <input type="date" name="data_fim" id="data_fim" value="<?= esc($data_fim) ?>" required class="shadow appearance-none border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="md:mt-5">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                Gerar Relatório
            </button>
        </div>
        <?= form_close() ?>
    </div>

    <!-- Resultados da DFC -->
    <?php if ($dfc_resultados): ?>
        <div class="bg-white p-6 rounded-lg shadow-xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Resultado do Período: <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?></h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-indigo-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-sm font-bold text-indigo-700 uppercase">Fluxo</th>
                            <th class="py-3 px-4 text-right text-sm font-bold text-indigo-700 uppercase">Entradas (Caixa)</th>
                            <th class="py-3 px-4 text-right text-sm font-bold text-indigo-700 uppercase">Saídas (Caixa)</th>
                            <th class="py-3 px-4 text-right text-sm font-bold text-indigo-700 uppercase">Caixa Líquido</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                        <!-- Lógica para garantir a ordem FCO, FCI, FCF -->
                        <?php
                        $fluxos = ['FCO', 'FCI', 'FCF'];
                        $descricoes = [
                            'FCO' => 'Fluxo de Caixa das Atividades Operacionais (Atividades-chave)',
                            'FCI' => 'Fluxo de Caixa das Atividades de Investimento (Ativos, Imobilizado)',
                            'FCF' => 'Fluxo de Caixa das Atividades de Financiamento (Dívidas, Capital)',
                        ];

                        // Garante que o array tenha todas as chaves (mesmo que com zero)
                        $resultados_formatados = array_merge([
                            'FCO' => ['entradas' => 0, 'saidas' => 0, 'liquido' => 0],
                            'FCI' => ['entradas' => 0, 'saidas' => 0, 'liquido' => 0],
                            'FCF' => ['entradas' => 0, 'saidas' => 0, 'liquido' => 0],
                        ], $dfc_resultados);

                        $total_liquido = 0;
                        ?>

                        <?php foreach ($fluxos as $fluxo):
                            $resultado = $resultados_formatados[$fluxo];
                            $liquido = $resultado['liquido'];
                            $total_liquido += $liquido;
                        ?>
                            <tr>
                                <td class="py-3 px-4 font-semibold text-gray-800">
                                    <span class="font-extrabold text-indigo-600 mr-2"><?= $fluxo ?></span> - <?= $descricoes[$fluxo] ?>
                                </td>
                                <td class="py-3 px-4 text-right text-green-600">R$ <?= number_format($resultado['entradas'], 2, ',', '.') ?></td>
                                <td class="py-3 px-4 text-right text-red-600">R$ (<?= number_format($resultado['saidas'], 2, ',', '.') ?>)</td>
                                <td class="py-3 px-4 text-right font-bold text-lg text-<?= $liquido >= 0 ? 'green-700' : 'red-700' ?>">
                                    R$ <?= number_format($liquido, 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <!-- Total -->
            <div class="mt-6 pt-4 border-t-2 border-indigo-200">
                <div class="flex justify-between items-center px-4">
                    <span class="text-xl font-bold text-gray-800">Variação Líquida do Caixa (Total DFC)</span>
                    <span class="text-3xl font-extrabold text-<?= $total_liquido >= 0 ? 'indigo-700' : 'red-700' ?>">
                        R$ <?= number_format($total_liquido, 2, ',', '.') ?>
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-2 text-right">Este valor representa o quanto o saldo de caixa aumentou ou diminuiu no período.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="p-4 text-center text-gray-500 bg-white rounded-lg shadow-md">
            Selecione o período e clique em "Gerar Relatório" para visualizar a DFC.
        </div>
    <?php endif; ?>
</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
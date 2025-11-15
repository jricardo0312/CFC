<!-- Simulação de um Template com Tailwind CSS (Para fins de Immersive) -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?></title>
    <!-- Incluir Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <div class="container">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Contas Pendentes de Liquidação</h1>
        <p class="mb-4 text-sm text-gray-600">Apenas transações com status "CONCLUÍDA" e data de caixa registrada entram no cálculo do DFC.</p>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100" role="alert">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100" role="alert">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <a href="<?= url_to('FinanceiroController::novaTransacao') ?>" class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300 mb-6">
            + Nova Conta a Pagar/Receber
        </a>

        <!-- Seção Contas a Receber -->
        <div class="mb-10">
            <h2 class="text-2xl font-semibold text-green-700 mb-4">Contas a Receber Pendentes (Entradas)</h2>
            <?= $this->renderSection('tabela_receber') ?>
        </div>

        <!-- Seção Contas a Pagar -->
        <div class="mb-10">
            <h2 class="text-2xl font-semibold text-red-700 mb-4">Contas a Pagar Pendentes (Saídas)</h2>
            <?= $this->renderSection('tabela_pagar') ?>
        </div>
    </div>

    <!-- Tabela Helper - Usada para renderizar ambas as tabelas (A Pagar e A Receber) -->
    <?php function render_tabela($transacoes, $tipo)
    { ?>
        <?php if (empty($transacoes)): ?>
            <p class="text-gray-500 italic">Não há contas <?= strtolower($tipo) ?> pendentes no momento. 🎉</p>
        <?php else: ?>
            <div class="overflow-x-auto shadow-lg rounded-lg">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-600 uppercase">Descrição</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-600 uppercase">Categoria (DFC)</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-600 uppercase">Pessoa</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-600 uppercase">Valor</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-600 uppercase">Vencimento</th>
                            <th class="py-3 px-4 text-center text-xs font-medium text-gray-600 uppercase">Ações de Caixa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($transacoes as $transacao): ?>
                            <tr>
                                <td class="py-4 px-4 text-sm font-medium text-gray-900"><?= esc($transacao['descricao']) ?></td>
                                <td class="py-4 px-4 text-sm text-gray-500"><?= esc($transacao['categoria_nome']) ?></td>
                                <td class="py-4 px-4 text-sm text-gray-500"><?= esc($transacao['pessoa_nome']) ?></td>
                                <td class="py-4 px-4 text-sm font-semibold text-<?= ($tipo == 'RECEBER' ? 'green' : 'red') ?>-600">
                                    R$ <?= number_format($transacao['valor'], 2, ',', '.') ?>
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-700"><?= date('d/m/Y', strtotime($transacao['data_vencimento'])) ?></td>
                                <td class="py-4 px-4 text-center">
                                    <!-- Formulário para Liquidação de Caixa -->
                                    <form action="<?= url_to('FinanceiroController::liquidarCaixa', $transacao['id']) ?>" method="post" class="inline-block">
                                        <!-- Campo de Data para DFC -->
                                        <input type="date" name="data_caixa_real" required class="text-sm p-1 border rounded-md focus:border-indigo-500 focus:ring-indigo-500 mr-2" title="Data do movimento de caixa real">
                                        <button type="submit" class="text-white text-sm px-3 py-1 rounded-lg transition duration-150 <?= ($tipo == 'RECEBER' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700') ?>">
                                            Confirmar <?= ($tipo == 'RECEBER' ? 'Recebimento' : 'Pagamento') ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php } ?>

    <?php $this->section('tabela_receber') ?>
    <?= render_tabela($contas_a_receber, 'RECEBER') ?>
    <?php $this->endSection() ?>

    <?php $this->section('tabela_pagar') ?>
    <?= render_tabela($contas_a_pagar, 'PAGAR') ?>
    <?php $this->endSection() ?>

</body>

</html>
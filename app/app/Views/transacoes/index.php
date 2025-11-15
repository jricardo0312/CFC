<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');
?>

<div class="container mx-auto px-4 py-8">

    <h1 class="text-3xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-300 flex justify-between items-center">
        <?= esc($titulo) ?>
        <a href="<?= route_to('nova_transacao') ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 text-sm">
            + Nova Transação
        </a>
    </h1>

    <!-- Área de Flash Messages -->
    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?= esc(session()->getFlashdata('sucesso')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?= esc(session()->getFlashdata('erro')) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">

        <?php if (empty($transacoes)): ?>
            <p class="text-center text-gray-500 py-10">
                🎉 Parabéns! Não há contas pendentes no momento. Cadastre uma nova para começar.
            </p>
        <?php else: ?>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descrição
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pessoa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vencimento
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Valor
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ação
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($transacoes as $transacao): ?>

                        <?php
                        // Estilos de cor baseados no tipo
                        $is_pagar = $transacao['tipo'] === 'PAGAR';
                        $valor_cor = $is_pagar ? 'text-red-600 font-bold' : 'text-green-600 font-bold';
                        $tipo_bg = $is_pagar ? 'bg-red-50' : 'bg-green-50';

                        // Formatação do valor para BRL
                        $valor_formatado = 'R$ ' . number_format($transacao['valor'], 2, ',', '.');

                        // Placeholder para Pessoa e Categoria (assumindo que o Controller faria o Join)
                        $pessoa_nome = 'ID: ' . $transacao['pessoa_id'];
                        $categoria_nome = 'Cat: ' . $transacao['categoria_id'];

                        // Verifica se está vencida
                        $data_venc = date_create($transacao['data_vencimento']);
                        $hoje = date_create(date('Y-m-d'));
                        $vencida_class = '';
                        if ($data_venc < $hoje) {
                            $vencida_class = 'bg-yellow-100 hover:bg-yellow-200';
                        }
                        ?>

                        <tr class="<?= $vencida_class ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= esc($transacao['id']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <div class="font-semibold"><?= esc($transacao['descricao']) ?></div>
                                <span class="text-xs text-gray-500"><?= esc($categoria_nome) ?></span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $tipo_bg ?> ml-2">
                                    <?= $is_pagar ? 'A PAGAR' : 'A RECEBER' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?= esc($pessoa_nome) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y', strtotime($transacao['data_vencimento'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm <?= $valor_cor ?>">
                                <?= esc($valor_formatado) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">

                                <!-- Formulário para Ação de Liquidação -->
                                <?= form_open(route_to('liquidar_caixa', $transacao['id']), ['class' => 'inline-block']) ?>
                                <button type="submit"
                                    onclick="return confirm('Tem certeza que deseja liquidar esta transação e movê-la para o Fluxo de Caixa?')"
                                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-1.5 px-3 rounded-lg text-xs shadow-md transition duration-300">
                                    Dar Baixa
                                </button>
                                <?= form_close() ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>

    </div>

    <p class="mt-4 text-sm text-gray-500">
        <span class="text-yellow-600 font-semibold">Nota:</span> A listagem de Contas Pendentes é baseada no Regime de Competência. Ao "Dar Baixa", a transação é movida para o Fluxo de Caixa (Regime de Caixa) e seu `status` muda para CONCLUIDA.
    </p>

</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
<?php
// Define o layout principal
$this->extend('layout/principal');

// Define a seção 'conteudo'
$this->section('conteudo');
?>

<div class="container">

    <div class="level mb-5">
        <div class="level-left">
            <div>
                <h1 class="title is-3 has-text-grey-darker mb-2">
                    <?= esc($titulo) ?>
                </h1>
                <p class="subtitle is-6 has-text-grey">
                    Gerenciamento de contas pendentes (Regime de Competência).
                </p>
            </div>
        </div>
        <div class="level-right">
            <a href="<?= route_to('nova_transacao') ?>" class="button is-link">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Nova Transação</span>
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="notification is-success is-light">
            <button class="delete"></button>
            <?= esc(session()->getFlashdata('sucesso')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <?= esc(session()->getFlashdata('erro')) ?>
        </div>
    <?php endif; ?>

    <div class="box">

        <?php if (empty($transacoes)): ?>

            <div class="has-text-centered py-6">
                <span class="icon is-large has-text-success mb-3">
                    <i class="fas fa-check-circle fa-3x"></i>
                </span>
                <h3 class="title is-4 has-text-grey">Tudo em dia!</h3>
                <p class="is-size-5 has-text-grey-light">
                    Não há contas pendentes no momento.
                </p>
            </div>

        <?php else: ?>

            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable is-vcentered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Pessoa</th>
                            <th>Vencimento</th>
                            <th class="has-text-right">Valor</th>
                            <th class="has-text-centered">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transacoes as $transacao): ?>

                            <?php
                            // --- LÓGICA DE APRESENTAÇÃO ---

                            $is_pagar = $transacao['tipo'] === 'PAGAR';

                            // Cores do Texto e Tags
                            $texto_valor = $is_pagar ? 'has-text-danger' : 'has-text-success';
                            $classe_tag  = $is_pagar ? 'is-danger is-light' : 'is-success is-light';
                            $label_tag   = $is_pagar ? 'A PAGAR' : 'A RECEBER';
                            $icone_tag   = $is_pagar ? 'fa-arrow-down' : 'fa-arrow-up';

                            // Formatação BRL
                            $valor_formatado = 'R$ ' . number_format($transacao['valor'], 2, ',', '.');

                            // Dados (Placeholder se não houver JOIN)
                            // Nota: Idealmente o Controller deve passar o nome, não o ID.
                            $pessoa_nome = isset($transacao['nome_pessoa']) ? $transacao['nome_pessoa'] : 'ID: ' . $transacao['pessoa_id'];
                            $categoria_nome = isset($transacao['nome_categoria']) ? $transacao['nome_categoria'] : 'Cat ID: ' . $transacao['categoria_id'];

                            // Verifica Vencimento
                            $data_venc = date_create($transacao['data_vencimento']);
                            $hoje = date_create(date('Y-m-d'));

                            $row_class = '';
                            $is_vencida = false;

                            // Se data de vencimento for menor que hoje, está vencida
                            if ($data_venc < $hoje) {
                                $row_class = 'has-background-warning-light'; // Amarelo suave
                                $is_vencida = true;
                            }
                            ?>

                            <tr class="<?= $row_class ?>">
                                <td class="has-text-grey-light is-size-7">
                                    #<?= esc($transacao['id']) ?>
                                </td>

                                <td>
                                    <p class="has-text-weight-bold has-text-grey-darker">
                                        <?= esc($transacao['descricao']) ?>
                                    </p>
                                    <div class="tags has-addons are-small mt-1">
                                        <span class="tag is-white">
                                            <?= esc($categoria_nome) ?>
                                        </span>
                                        <span class="tag <?= $classe_tag ?>">
                                            <span class="icon is-small mr-1">
                                                <i class="fas <?= $icone_tag ?>"></i>
                                            </span>
                                            <?= $label_tag ?>
                                        </span>
                                        <?php if ($is_vencida): ?>
                                            <span class="tag is-warning has-text-weight-bold">
                                                VENCIDA
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="icon-text">
                                        <span class="icon has-text-grey-light is-small">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <span><?= esc($pessoa_nome) ?></span>
                                    </span>
                                </td>

                                <td>
                                    <span class="icon-text <?= $is_vencida ? 'has-text-danger has-text-weight-bold' : '' ?>">
                                        <span class="icon is-small">
                                            <i class="fas fa-calendar-day"></i>
                                        </span>
                                        <span><?= date('d/m/Y', strtotime($transacao['data_vencimento'])) ?></span>
                                    </span>
                                </td>

                                <td class="has-text-right has-text-weight-bold <?= $texto_valor ?>">
                                    <?= esc($valor_formatado) ?>
                                </td>

                                <td class="has-text-centered">
                                    <?= form_open(route_to('liquidar_caixa', $transacao['id']), ['class' => 'is-inline js-form-confirm']) ?>
                                    <button type="submit"
                                        class="button is-success is-small is-light is-outlined"
                                        title="Dar Baixa (Liquidar)"
                                        onclick="return confirm('Tem certeza que deseja liquidar esta transação? Ela irá para o Fluxo de Caixa.')">
                                        <span class="icon">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        <span>Baixar</span>
                                    </button>
                                    <?= form_close() ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </div>

    <article class="message is-warning is-small mt-5">
        <div class="message-body">
            <strong>Nota:</strong> A listagem de <em>Contas Pendentes</em> é baseada no <strong>Regime de Competência</strong>.
            Ao clicar em "Baixar", a transação é efetivada, movida para o Fluxo de Caixa (Regime de Caixa) e seu status muda para CONCLUÍDA.
        </div>
    </article>

</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
<?= $this->extend('layout/principal'); ?>
<?= $this->section('conteudo'); ?>

<?php
// --- LÓGICA DE ORDENAÇÃO E URL ---
$currentOrdem   = isset($ordem) ? $ordem : 'id';
$currentDirecao = isset($direcao) ? $direcao : 'asc';

$proximaDirId   = ($currentOrdem === 'id' && $currentDirecao === 'asc') ? 'desc' : 'asc';
$proximaDirNome = ($currentOrdem === 'nome' && $currentDirecao === 'asc') ? 'desc' : 'asc';

// Links para ordenação
$linkId   = site_url("pessoas?ordem=id&direcao={$proximaDirId}");
$linkNome = site_url("pessoas?ordem=nome&direcao={$proximaDirNome}");

// Helper para definir o ícone correto do Font Awesome
function getIconeOrdenacao($colunaAtual, $colunaAlvo, $direcao)
{
    if ($colunaAtual !== $colunaAlvo) {
        return '<i class="fas fa-sort has-text-grey-light ml-1"></i>'; // Neutro
    }
    return ($direcao === 'asc')
        ? '<i class="fas fa-sort-up has-text-link ml-1"></i>'
        : '<i class="fas fa-sort-down has-text-link ml-1"></i>';
}

$iconeId   = getIconeOrdenacao($currentOrdem, 'id', $currentDirecao);
$iconeNome = getIconeOrdenacao($currentOrdem, 'nome', $currentDirecao);
?>

<div class="container">

    <div class="level mb-5">
        <div class="level-left">
            <div>
                <h1 class="title is-3 has-text-grey-darker mb-2">
                    <?= esc($titulo ?? 'Lista de Pessoas') ?>
                </h1>
                <p class="subtitle is-6 has-text-grey">Gerencie seus clientes e fornecedores</p>
            </div>
        </div>
        <div class="level-right">
            <a href="<?= site_url('pessoas/nova') ?>" class="button is-link">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Nova Pessoa</span>
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="notification is-success is-light">
            <button class="delete"></button>
            <?= session()->getFlashdata('sucesso') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <?= session()->getFlashdata('erro') ?>
        </div>
    <?php endif; ?>

    <div class="box">
        <?php if (empty($pessoas)): ?>

            <div class="has-text-centered py-6">
                <span class="icon is-large has-text-grey-light mb-3">
                    <i class="fas fa-users-slash fa-3x"></i>
                </span>
                <p class="is-size-5 has-text-grey">Nenhuma pessoa cadastrada ainda.</p>
            </div>

        <?php else: ?>

            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable is-vcentered">
                    <thead>
                        <tr>
                            <th>
                                <a href="<?= $linkId ?>" class="has-text-grey-darker is-flex is-align-items-center" title="Ordenar por ID">
                                    ID <?= $iconeId ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= $linkNome ?>" class="has-text-grey-darker is-flex is-align-items-center" title="Ordenar por Nome">
                                    NOME <?= $iconeNome ?>
                                </a>
                            </th>
                            <th>Documento</th>
                            <th>E-mail</th>
                            <th class="has-text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pessoas as $pessoa): ?>
                            <tr>
                                <td class="has-text-grey"><?= esc($pessoa['id']) ?></td>
                                <td class="has-text-weight-medium text-dark">
                                    <?= esc($pessoa['nome']) ?>
                                    <?php if (isset($pessoa['tipo_pessoa'])): ?>
                                        <span class="tag is-white is-small text-gray-500 border ml-2">
                                            <?= esc($pessoa['tipo_pessoa']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="tag is-light is-small has-text-weight-bold mr-1">
                                        <?= esc($pessoa['tipo_documento']) ?>
                                    </span>
                                    <span class="is-family-monospace"><?= esc($pessoa['documento']) ?></span>
                                </td>
                                <td><?= esc($pessoa['email']) ?></td>
                                <td class="has-text-right">
                                    <div class="buttons is-right are-small">

                                        <a href="<?= site_url('pessoas/editar/' . $pessoa['id']) ?>" class="button is-info is-outlined" title="Editar">
                                            <span class="icon">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                        </a>

                                        <form action="<?= site_url('pessoas/excluir/' . $pessoa['id']) ?>" method="post" class="is-inline js-form-delete">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">

                                            <button type="submit" class="button is-danger is-outlined" title="Excluir">
                                                <span class="icon">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>

    <div class="section pt-0">
        <nav class="pagination is-centered" role="navigation" aria-label="pagination">
            <?= $pager->links('default', 'default_full') ?>
        </nav>
    </div>

</div>

<?php $this->endSection(); ?>
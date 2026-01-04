<?php
// Define o layout principal que esta view deve herdar
$this->extend('layout/principal');

// Define a seção 'conteudo' que será injetada no layout
$this->section('conteudo');
?>

<div class="container">

    <div class="level mb-5">
        <div class="level-left">
            <div>
                <h1 class="title is-3 has-text-grey-darker"><?= esc($titulo) ?></h1>
                <p class="subtitle is-6 has-text-grey">Gerencie as categorias de receitas e despesas.</p>
            </div>
        </div>
        <div class="level-right">
            <a href="<?= route_to('categorias_nova') ?>" class="button is-link">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Nova Categoria</span>
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
        <?php if (empty($categorias)): ?>

            <div class="has-text-centered py-6">
                <span class="icon is-large has-text-grey-light mb-2">
                    <i class="fas fa-folder-open fa-3x"></i>
                </span>
                <p class="has-text-grey">Nenhuma categoria cadastrada ainda.</p>
            </div>

        <?php else: ?>

            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable is-vcentered">
                    <thead>
                        <tr>
                            <th>Nome da Categoria</th>
                            <th>Mapeamento DFC</th>
                            <th class="has-text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td class="has-text-weight-medium">
                                    <?= esc($categoria['nome']) ?>
                                </td>

                                <td>
                                    <?php
                                    $fluxo = esc($categoria['tipo_fluxo']);
                                    // Definição de cores baseada no Bulma
                                    $class = 'is-light'; // Padrão
                                    if ($fluxo === 'FCO') $class = 'is-info is-light';      // Azul claro
                                    if ($fluxo === 'FCI') $class = 'is-warning is-light';   // Amarelo claro
                                    if ($fluxo === 'FCF') $class = 'is-success is-light';   // Verde claro
                                    ?>
                                    <span class="tag <?= $class ?>">
                                        <?= $fluxo ?>
                                    </span>
                                </td>

                                <td class="has-text-right">
                                    <div class="buttons is-right are-small">

                                        <a href="<?= route_to('categorias_editar', $categoria['id']) ?>" class="button is-info is-outlined" title="Editar">
                                            <span class="icon">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                        </a>

                                        <form action="<?= route_to('categorias_excluir', $categoria['id']) ?>" method="post" class="is-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria? Esta ação é irreversível.');">

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

</div>

<?php
// Fecha a seção 'conteudo'
$this->endSection();
?>
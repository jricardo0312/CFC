<?php
$this->extend('layout/principal');
$this->section('titulo');
echo esc($titulo);
$this->endSection();

$this->section('conteudo');
?>

<style>
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        cursor: pointer;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        /* Efeito suave de subida */
        box-shadow: 0 0.5em 1em -0.125em rgba(10, 10, 10, 0.1), 0 0px 0 1px rgba(10, 10, 10, 0.02);
    }

    .border-l-success {
        border-left: 5px solid #48c774;
    }

    .border-l-warning {
        border-left: 5px solid #ffdd57;
    }
</style>

<section class="section">
    <div class="container">

        <div class="block mb-6">
            <h1 class="title is-2 has-text-link-dark"><?= esc($titulo) ?></h1>
            <p class="subtitle is-4 has-text-grey"><?= esc($subtitulo) ?></p>
        </div>

        <div class="columns is-multiline is-variable is-6 mb-6">

            <div class="column is-one-third-desktop is-half-tablet">
                <a href="<?= route_to('nova_transacao') ?>">
                    <div class="box hover-card">
                        <article class="media is-align-items-center">
                            <figure class="media-left">
                                <span class="icon is-large has-text-link has-background-link-light" style="border-radius: 50%; width: 64px; height: 64px;">
                                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                </span>
                            </figure>
                            <div class="media-content">
                                <div class="content">
                                    <p class="title is-5 has-text-grey-darker mb-1">Lançamento</p>
                                    <p class="subtitle is-6 has-text-grey">Contas a Pagar e Receber</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </a>
            </div>

            <div class="column is-one-third-desktop is-half-tablet">
                <a href="<?= route_to('pessoas_index') ?>">
                    <div class="box hover-card">
                        <article class="media is-align-items-center">
                            <figure class="media-left">
                                <span class="icon is-large has-text-info has-background-info-light" style="border-radius: 50%; width: 64px; height: 64px;">
                                    <i class="fas fa-users fa-2x"></i>
                                </span>
                            </figure>
                            <div class="media-content">
                                <div class="content">
                                    <p class="title is-5 has-text-grey-darker mb-1">Pessoas</p>
                                    <p class="subtitle is-6 has-text-grey">Clientes e Fornecedores</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </a>
            </div>

            <div class="column is-one-third-desktop is-half-tablet">
                <a href="<?= route_to('categorias_index') ?>">
                    <div class="box hover-card">
                        <article class="media is-align-items-center">
                            <figure class="media-left">
                                <span class="icon is-large has-text-primary has-background-primary-light" style="border-radius: 50%; width: 64px; height: 64px;">
                                    <i class="fas fa-tags fa-2x"></i>
                                </span>
                            </figure>
                            <div class="media-content">
                                <div class="content">
                                    <p class="title is-5 has-text-grey-darker mb-1">Mapeamento</p>
                                    <p class="subtitle is-6 has-text-grey">Plano de Contas DFC</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </a>
            </div>
        </div>

        <h2 class="title is-3 has-text-grey-darker mt-6 mb-4 pb-2" style="border-bottom: 1px solid #dbdbdb;">
            Relatórios e Fluxo de Caixa
        </h2>

        <div class="columns is-multiline is-variable is-6">

            <div class="column is-half-desktop">
                <a href="<?= route_to('relatorio_dfc') ?>">
                    <div class="box hover-card border-l-success">
                        <article class="media is-align-items-center">
                            <figure class="media-left">
                                <span class="icon is-large has-text-success has-background-success-light" style="border-radius: 50%; width: 64px; height: 64px;">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </span>
                            </figure>
                            <div class="media-content">
                                <div class="content">
                                    <p class="title is-5 has-text-grey-darker mb-1">Demonstração DFC</p>
                                    <p class="subtitle is-6 has-text-grey">Análise consolidada (FCO, FCI, FCF).</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </a>
            </div>

            <div class="column is-half-desktop">
                <a href="<?= route_to('financeiro_index') ?>">
                    <div class="box hover-card border-l-warning">
                        <article class="media is-align-items-center">
                            <figure class="media-left">
                                <span class="icon is-large has-text-warning has-background-warning-light" style="border-radius: 50%; width: 64px; height: 64px;">
                                    <i class="fas fa-clock fa-2x"></i>
                                </span>
                            </figure>
                            <div class="media-content">
                                <div class="content">
                                    <p class="title is-5 has-text-grey-darker mb-1">Contas Pendentes</p>
                                    <p class="subtitle is-6 has-text-grey">Itens a receber ou pagar.</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

<?php $this->endSection(); ?>
<?php
// Este arquivo é o esqueleto HTML que todas as views estendem.
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('titulo') ?? 'Sistema DFC' ?> | Sistema Financeiro</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/bulma.min.css') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/relatorio.css') ?>">

    <style>
        /* Fonte Inter (Opcional, mas mantém o estilo clean) */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            /* Garante o Sticky Footer (rodapé no fundo) */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f5f5f5;
            /* Equivalente ao bg-gray-100 */
        }

        /* O conteúdo principal ocupa o espaço restante */
        main.section {
            flex: 1;
        }

        /* Ajuste fino para a Navbar ficar com sombra suave */
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <nav class="navbar is-white" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item has-text-weight-bold has-text-link is-size-5" href="<?= route_to('dashboard') ?>">
                    SALUTEM Terapias Integradas
                </a>

                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>

            <div id="navbarBasicExample" class="navbar-menu">
                <div class="navbar-start">
                    <a href="<?= route_to('dashboard') ?>" class="navbar-item has-text-weight-medium">
                        Painel
                    </a>
                    <a href="<?= route_to('financeiro_index') ?>" class="navbar-item has-text-grey-dark">
                        Transações
                    </a>
                    <a href="<?= route_to('pessoas_index') ?>" class="navbar-item has-text-grey-dark">
                        Pessoas
                    </a>
                    <a href="<?= route_to('categorias_index') ?>" class="navbar-item has-text-grey-dark">
                        Categorias
                    </a>

                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link has-text-grey-dark">
                            Relatórios
                        </a>
                        <div class="navbar-dropdown">
                            <a href="<?= route_to('relatorio_dfc') ?>" class="navbar-item">
                                Relatório DFC
                            </a>
                            <a href="<?= route_to('relatorio_index') ?>" class="navbar-item">
                                Relatório Transações
                            </a>
                            </a>
                            <a href="<?= route_to('relatorio/tipocliente') ?>" class="navbar-item">
                                Relatório por tipo de cliente
                            </a>






                        </div>
                    </div>
                </div>

                <div class="navbar-end">
                    <div class="navbar-item">
                        <div class="buttons">
                            <a href="<?= url_to('Auth::logout') ?>" class="button is-danger is-small">
                                <strong>Sair</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <?php if (session()->has('sucesso') || session()->has('erro')): ?>
        <div class="container mt-4">
            <?php if (session()->has('sucesso')): ?>
                <div class="notification is-success is-light">
                    <button class="delete"></button>
                    <?= session()->getFlashdata('sucesso') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->has('erro')): ?>
                <div class="notification is-danger is-light">
                    <button class="delete"></button>
                    <?= session()->getFlashdata('erro') ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>


    <main class="section">
        <div class="container">
            <?= $this->renderSection('conteudo') ?>
        </div>
    </main>

    <footer class="footer has-background-white" style="padding: 2rem 1.5rem;">
        <div class="content has-text-centered">
            <p class="has-text-grey">
                &copy; <?= date('Y') ?> <strong>DFC System</strong> - Desenvolvido por: José Ricardo.
            </p>
        </div>
    </footer>

    <?= $this->renderSection('scripts') ?>

    <script src="<?= base_url('assets/js/main.js') ?>"></script>

</body>

</html>
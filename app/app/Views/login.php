<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/bulma.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
</head>

<body>

    <section class="hero is-light is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">

                    <div class="column is-4-desktop is-6-tablet">

                        <div class="has-text-centered mb-5">
                            <h2 class="title is-3 has-text-grey-darker">Acessar Conta</h2>
                            <p class="subtitle is-6 has-text-grey">Bem-vindo de volta!</p>
                        </div>

                        <div class="box">

                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="notification is-success is-light is-small">
                                    <button class="delete"></button>
                                    <?= session()->getFlashdata('success') ?>
                                </div>
                            <?php endif; ?>

                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="notification is-danger is-light is-small">
                                    <button class="delete"></button>
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?= url_to('Auth::tentarLogin') ?>" method="POST">

                                <?= csrf_field() ?>

                                <div class="field">
                                    <label class="label" for="email">Email</label>
                                    <div class="control has-icons-left">
                                        <input type="email" name="email" id="email"
                                            class="input"
                                            value="<?= old('email') ?>"
                                            placeholder="seu@email.com"
                                            required autofocus>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label" for="senha">Senha</label>
                                    <div class="control has-icons-left">
                                        <input type="password" name="senha" id="senha"
                                            class="input"
                                            placeholder="Sua senha"
                                            required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field mt-5">
                                    <button type="submit" class="button is-link is-fullwidth">
                                        Entrar
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="has-text-centered mt-4">
                            <a href="<?= url_to('Auth::register') ?>" class="is-size-7 has-text-link">
                                Não tem conta? <strong>Cadastre-se</strong>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
                const $notification = $delete.parentNode;
                $delete.addEventListener('click', () => {
                    $notification.parentNode.removeChild($notification);
                });
            });
        });
    </script>
</body>

</html>
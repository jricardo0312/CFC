<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Criar Conta</title>

    <link rel="stylesheet" href="<?= base_url('assets/css/bulma.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
</head>

<body>

    <section class="hero is-light is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">

                    <div class="column is-5-tablet is-4-desktop">

                        <div class="has-text-centered mb-5">
                            <h2 class="title is-3 has-text-grey-darker">Criar Nova Conta</h2>
                            <p class="subtitle is-6 has-text-grey">Preencha os dados abaixo</p>
                        </div>

                        <div class="box">

                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="notification is-danger is-light is-small">
                                    <button class="delete"></button>
                                    <p class="has-text-weight-bold mb-1">Por favor, corrija os erros:</p>
                                    <ul class="ml-4" style="list-style-type: disc;">
                                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form action="<?= url_to('Auth::createAccount') ?>" method="POST">

                                <?= csrf_field() ?>

                                <div class="field">
                                    <label class="label" for="nome">Nome Completo</label>
                                    <div class="control has-icons-left">
                                        <input type="text" name="nome" id="nome" required
                                            class="input"
                                            value="<?= old('nome') ?>"
                                            placeholder="Seu nome">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label" for="email">Email</label>
                                    <div class="control has-icons-left">
                                        <input type="email" name="email" id="email" required
                                            class="input"
                                            value="<?= old('email') ?>"
                                            placeholder="seu@email.com">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label" for="senha">Senha</label>
                                    <div class="control has-icons-left">
                                        <input type="password" name="senha" id="senha" required
                                            class="input"
                                            placeholder="Mínimo 8 caracteres">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                    <p class="help">Use uma senha forte com letras e números.</p>
                                </div>

                                <div class="field">
                                    <label class="label" for="confirma_senha">Confirmar Senha</label>
                                    <div class="control has-icons-left">
                                        <input type="password" name="confirma_senha" id="confirma_senha" required
                                            class="input"
                                            placeholder="Repita a senha">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field mt-5">
                                    <button type="submit" class="button is-success is-fullwidth">
                                        <strong>Cadastrar</strong>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="has-text-centered mt-4">
                            <a href="<?= url_to('Auth::login') ?>" class="is-size-7 has-text-link">
                                Já tem conta? <strong>Fazer Login</strong>
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
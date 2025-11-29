<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="/css/tailwind.css" rel="stylesheet"> -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-lg p-8 space-y-6 bg-white rounded-lg shadow-xl">
        <h2 class="text-3xl font-bold text-center text-gray-900">Criar Nova Conta</h2>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <p class="font-bold">Por favor, corrija os seguintes erros:</p>
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li class="list-disc ml-4"><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="space-y-4" action="<?= url_to('Auth::createAccount') ?>" method="POST">

            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input type="text" name="nome" id="nome" required
                    class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    value="<?= old('nome') ?>" placeholder="Seu nome">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    value="<?= old('email') ?>" placeholder="seu@email.com">
            </div>

            <div>
                <label for="senha" class="block text-sm font-medium text-gray-700">Senha (mínimo 8 caracteres)</label>
                <input type="password" name="senha" id="senha" required
                    class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Crie uma senha forte">
            </div>

            <div>
                <label for="confirma_senha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                <input type="password" name="confirma_senha" id="confirma_senha" required
                    class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Repita a senha">
            </div>

            <button type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Cadastrar
            </button>
        </form>

        <div class="text-sm text-center">
            <a href="<?= url_to('Auth::login') ?>" class="font-medium text-indigo-600 hover:text-indigo-500">
                Já tem conta? Fazer Login
            </a>
        </div>
    </div>
</body>

</html>
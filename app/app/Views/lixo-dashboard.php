<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="/css/tailwind.css" rel="stylesheet"> -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-2xl p-8 space-y-6 bg-white rounded-lg shadow-xl text-center">
        <h2 class="text-3xl font-bold text-gray-900">👋 Bem-vindo(a), <?= session()->get('nome') ?>!</h2>
        <p class="text-lg text-gray-600">Você está logado no sistema.</p>

        <a href="<?= url_to('Auth::logout') ?>"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Sair
        </a>
    </div>
</body>

</html>
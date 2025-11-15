<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Clínica DFC App' ?></title>
    <!-- Incluir Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .nav-link {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <header class="bg-white shadow-md">
        <div class="container flex justify-between items-center">
            <h1 class="text-xl font-bold text-indigo-600">Clínica DFC Manager</h1>
            <nav class="flex space-x-4">
                <a href="<?= url_to('FinanceiroController::index') ?>" class="nav-link text-gray-600 hover:bg-gray-100 hover:text-indigo-600 transition">Pendentes</a>
                <a href="<?= url_to('FinanceiroController::novaTransacao') ?>" class="nav-link text-gray-600 hover:bg-gray-100 hover:text-indigo-600 transition">Novo Lançamento</a>
                <a href="<?= url_to('FinanceiroController::relatorioDFC') ?>" class="nav-link bg-indigo-100 text-indigo-600 font-semibold transition">Relatório DFC</a>
            </nav>
        </div>
    </header>
    <main class="container py-8">
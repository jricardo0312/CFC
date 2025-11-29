<!-- Simulação de um Template com Tailwind CSS -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Nova Transação' ?></title>
    <!-- Incluir Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 1.5rem;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <div class="container">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">Cadastrar Nova Transação</h1>
        <p class="mb-6 text-sm text-gray-600">As transações aqui cadastradas iniciam como PENDENTES.</p>

        <div class="bg-white p-6 rounded-lg shadow-xl">
            <form action="<?= url_to('FinanceiroController::salvarTransacao') ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($validation)): ?>
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <!-- Tipo da Transação -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tipo">Tipo</label>
                    <select name="tipo" id="tipo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Selecione --</option>
                        <option value="RECEBER" <?= set_select('tipo', 'RECEBER') ?>>Conta a Receber (Entrada)</option>
                        <option value="PAGAR" <?= set_select('tipo', 'PAGAR') ?>>Conta a Pagar (Saída)</option>
                    </select>
                </div>

                <!-- Categoria Financeira (Mapeamento DFC) -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="categoria_id">Categoria (DFC)</label>
                    <select name="categoria_id" id="categoria_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= set_select('categoria_id', $cat['id']) ?>>
                                [<?= $cat['tipo_fluxo'] ?>] <?= esc($cat['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">O código entre colchetes ([FCO], [FCI], [FCF]) define onde a transação irá aparecer na Demonstração dos Fluxos de Caixa.</p>
                </div>

                <!-- Pessoa (Cliente/Fornecedor/Sócio) -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="pessoa_id">Pessoa Envolvida</label>
                    <select name="pessoa_id" id="pessoa_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($pessoas as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= set_select('pessoa_id', $p['id']) ?>>
                                [<?= $p['tipo_pessoa'] ?>] <?= esc($p['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Valor e Vencimento -->
                <div class="flex space-x-4 mb-4">
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="valor">Valor</label>
                        <input type="number" step="0.01" name="valor" id="valor" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?= set_value('valor') ?>">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="data_vencimento">Data de Vencimento (Competência)</label>
                        <input type="date" name="data_vencimento" id="data_vencimento" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?= set_value('data_vencimento') ?>">
                    </div>
                </div>

                <!-- Descrição -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="descricao">Descrição</label>
                    <textarea name="descricao" id="descricao" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= set_value('descricao') ?></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                        Salvar Transação
                    </button>
                    <a href="<?= url_to('FinanceiroController::index') ?>" class="inline-block align-baseline font-bold text-sm text-indigo-600 hover:text-indigo-800">
                        Voltar para o Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
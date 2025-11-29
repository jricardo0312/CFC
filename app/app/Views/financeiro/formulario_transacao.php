<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Nova Transação' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.2.2/css/tom-select.css" rel="stylesheet" />
    
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 1.5rem;
        }
        /* Pequeno ajuste para o Tom Select combinar melhor com o Tailwind padrão */
        .ts-control {
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            border-color: #e5e7eb; /* border-gray-200 */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .ts-wrapper.focus .ts-control {
            border-color: #6366f1; /* ring-indigo-500 */
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
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

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tipo">Tipo</label>
                    <select name="tipo" id="tipo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Selecione --</option>
                        <option value="RECEBER" <?= set_select('tipo', 'RECEBER') ?>>Conta a Receber (Entrada)</option>
                        <option value="PAGAR" <?= set_select('tipo', 'PAGAR') ?>>Conta a Pagar (Saída)</option>
                    </select>
                </div>

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

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="pessoa_id">Pessoa Envolvida</label>
                    <select name="pessoa_id" id="pessoa_id" placeholder="Digite o nome para buscar..." autocomplete="off">
                        <option value="">Selecione ou digite...</option>
                        <?php foreach ($pessoas as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= set_select('pessoa_id', $p['id']) ?>>
                                [<?= $p['tipo_pessoa'] ?>] <?= esc($p['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
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

    <!-- <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.2.2/js/tom-select.complete.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        new TomSelect("#pessoa_id", {
            create: false,
            // Define explicitamente onde buscar (no texto da opção)
            searchField: ['text'], 
            
            // AQUI ESTÁ O SEGREDO:
            // 1º Prioridade: Relevância ($score) - quem tem a palavra exata aparece primeiro
            // 2º Prioridade: Ordem alfabética (text) - para desempate
            sortField: [
                { field: "$score", direction: "desc" }, 
                { field: "text", direction: "asc" }
            ],

            // Configurações para melhorar a experiência
            placeholder: "Digite para buscar...",
            
            // Remove acentos da busca (Célia encontra Celia e vice-versa)
            diacritics: true, 
            
            // Renderização: Destaque visual
            render: {
                // Personaliza como a opção aparece na lista (opcional)
                option: function(data, escape) {
                    return '<div class="py-2">' + escape(data.text) + '</div>';
                },
                // Personaliza a mensagem quando não encontra nada
                no_results: function(data, escape) {
                    return '<div class="no-results p-2 text-gray-500">Nenhum resultado para "'+ escape(data.input) +'"</div>';
                }
            }
        });
    });
</script>
</body>

</html>

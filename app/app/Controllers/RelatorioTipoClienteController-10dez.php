<?php

namespace App\Controllers;

use App\Models\RelatorioModel;

class RelatorioTipoClienteController extends BaseController
{
    public function index()
    {
        $model = new RelatorioModel();

        // DEBUG: Testar conexão e consultas básicas
        if ($this->request->getGet('debug') == '1') {
            return $this->debugCompleto();
        }

        // Obter parâmetros do filtro
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $tipoCliente = $this->request->getGet('tipo_cliente');
        $tipoTransacao = $this->request->getGet('tipo_transacao');

        // DEBUG: Mostrar filtros aplicados
        $data['debug_filtros'] = [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'tipo_cliente' => $tipoCliente,
            'tipo_transacao' => $tipoTransacao
        ];

        // Buscar dados
        $data['transacoes'] = $model->getTransacoesPorTipoCliente($dataInicio, $dataFim, $tipoCliente, $tipoTransacao);
        $data['totais'] = $model->getTotalPorTipoCliente($dataInicio, $dataFim, $tipoCliente, $tipoTransacao);
        $data['tiposCliente'] = $model->getTiposCliente();
        $data['tiposTransacao'] = $model->getTiposTransacao();
        $data['resumoGeral'] = $model->getResumoGeral($dataInicio, $dataFim, $tipoCliente, $tipoTransacao);

        // DEBUG: Verificar resultados
        $data['debug_info'] = [
            'total_transacoes' => count($data['transacoes']),
            'total_tipos_cliente' => count($data['tiposCliente']),
            'coluna_relacionamento' => $model->getColunaRelacionamento()
        ];

        // Passar filtros para a view
        $data['filtros'] = [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'tipo_cliente' => $tipoCliente,
            'tipo_transacao' => $tipoTransacao
        ];

        // DEBUG: Ver todos os dados
        if ($this->request->getGet('showdata') == '1') {
            echo "<pre>";
            echo "=== DEBUG DATA ===\n";
            print_r($data);
            echo "</pre>";
            return;
        }

        return view('relatorio_tipo_cliente', $data);
    }

    public function exportar()
    {
        // ... manter código existente ...
    }

    public function debugCompleto()
    {
        $model = new RelatorioModel();
        $db = \Config\Database::connect();

        echo "<h1>DEBUG COMPLETO - RELATÓRIO</h1>";

        echo "<h2>1. Teste de Conexão:</h2>";
        try {
            $db->connect();
            echo "✅ Conexão com banco de dados OK<br>";
        } catch (\Exception $e) {
            echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
        }

        echo "<h2>2. Estrutura da tabela transacoes:</h2>";
        $transacoesFields = $db->getFieldData('transacoes');
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Nome</th><th>Tipo</th><th>Chave</th></tr>";
        foreach ($transacoesFields as $coluna) {
            $isKey = $coluna->primary_key ? 'PRIMARY' : '';
            echo "<tr><td><strong>{$coluna->name}</strong></td><td>{$coluna->type}</td><td>{$isKey}</td></tr>";
        }
        echo "</table>";

        echo "<h2>3. Estrutura da tabela pessoas:</h2>";
        $pessoasFields = $db->getFieldData('pessoas');
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Nome</th><th>Tipo</th><th>Chave</th></tr>";
        foreach ($pessoasFields as $coluna) {
            $isKey = $coluna->primary_key ? 'PRIMARY' : '';
            echo "<tr><td><strong>{$coluna->name}</strong></td><td>{$coluna->type}</td><td>{$isKey}</td></tr>";
        }
        echo "</table>";

        echo "<h2>4. Teste de consulta básica (transacoes):</h2>";
        $query = $db->query("SELECT COUNT(*) as total FROM transacoes");
        $totalTransacoes = $query->getRow()->total;
        echo "Total de registros em transacoes: <strong>{$totalTransacoes}</strong><br>";

        if ($totalTransacoes > 0) {
            $query = $db->query("SELECT * FROM transacoes LIMIT 3");
            echo "Primeiros 3 registros:<br>";
            echo "<pre>";
            print_r($query->getResultArray());
            echo "</pre>";
        }

        echo "<h2>5. Teste de consulta básica (pessoas):</h2>";
        $query = $db->query("SELECT COUNT(*) as total FROM pessoas");
        $totalPessoas = $query->getRow()->total;
        echo "Total de registros em pessoas: <strong>{$totalPessoas}</strong><br>";

        if ($totalPessoas > 0) {
            $query = $db->query("SELECT id, nome, tipo_cliente FROM pessoas LIMIT 3");
            echo "Primeiros 3 registros:<br>";
            echo "<pre>";
            print_r($query->getResultArray());
            echo "</pre>";
        }

        echo "<h2>6. Teste de JOIN manual:</h2>";
        $colunaRel = $model->getColunaRelacionamento();
        echo "Coluna de relacionamento detectada: <strong>{$colunaRel}</strong><br>";

        $sqlTeste = "SELECT t.*, p.nome, p.tipo_cliente 
                    FROM transacoes t 
                    LEFT JOIN pessoas p ON p.id = t.{$colunaRel} 
                    LIMIT 3";

        echo "SQL testado: <code>{$sqlTeste}</code><br>";

        try {
            $query = $db->query($sqlTeste);
            $resultados = $query->getResultArray();
            echo "Resultados do JOIN:<br>";
            echo "<pre>";
            print_r($resultados);
            echo "</pre>";

            if (empty($resultados)) {
                echo "⚠️ JOIN não retornou dados. Possíveis causas:<br>";
                echo "1. A coluna {$colunaRel} não é a correta<br>";
                echo "2. Não há dados relacionados entre as tabelas<br>";
                echo "3. Valores na coluna {$colunaRel} não correspondem a IDs em pessoas<br>";
            }
        } catch (\Exception $e) {
            echo "❌ Erro no JOIN: " . $e->getMessage() . "<br>";

            // Tentar descobrir a coluna correta
            echo "<h3>7. Tentando descobrir coluna correta:</h3>";
            $query = $db->query("SHOW COLUMNS FROM transacoes");
            $colunas = $query->getResultArray();

            echo "Colunas disponíveis em transacoes que podem ser a chave estrangeira:<br>";
            foreach ($colunas as $coluna) {
                if (preg_match('/(pessoa|cliente|id_pessoa|pessoa_id|id_cliente|cliente_id)/i', $coluna['Field'])) {
                    echo "- <strong>{$coluna['Field']}</strong> ({$coluna['Type']})<br>";
                }
            }
        }

        echo "<h2>8. Testar método do Model:</h2>";
        $testeModel = $model->getTransacoesPorTipoCliente(null, null, null, null);
        echo "Total retornado pelo Model (sem filtros): " . count($testeModel) . "<br>";

        echo "<h2>9. SQL gerado pelo Model:</h2>";
        // Para ver o SQL gerado, precisamos modificar temporariamente o Model
        // Adicione isto temporariamente ao método getTransacoesPorTipoCliente:
        // $builder->getCompiledSelect(); // Retorna o SQL sem executar

        echo "<hr>";
        echo "<h3>Soluções possíveis:</h3>";
        echo "<ol>";
        echo "<li>Verifique se a coluna de relacionamento está correta</li>";
        echo "<li>Verifique se há dados nas tabelas</li>";
        echo "<li>Verifique se os IDs em transacoes.{$colunaRel} existem em pessoas.id</li>";
        echo "<li>Teste diferentes nomes de colunas no método setColunaRelacionamento()</li>";
        echo "</ol>";

        echo "<h3>Teste rápido:</h3>";
        echo '<a href="' . site_url('relatorio/tipocliente?debug=teste') . '">Clique aqui para testar diferentes colunas</a>';
    }

    public function testarColunas()
    {
        $model = new RelatorioModel();
        $db = \Config\Database::connect();

        echo "<h1>TESTAR DIFERENTES COLUNAS</h1>";

        // Possíveis nomes de colunas
        $colunasTeste = ['id_pessoa', 'pessoa_id', 'cliente_id', 'id_cliente', 'pessoa', 'cliente', 'fk_pessoa'];

        echo "<h2>Colunas encontradas na tabela transacoes:</h2>";
        $query = $db->query("SHOW COLUMNS FROM transacoes");
        $colunasExistentes = $query->getResultArray();

        echo "<ul>";
        foreach ($colunasExistentes as $coluna) {
            echo "<li><strong>{$coluna['Field']}</strong> ({$coluna['Type']})</li>";
        }
        echo "</ul>";

        echo "<h2>Testando cada possível coluna:</h2>";

        foreach ($colunasTeste as $colunaTeste) {
            echo "<h3>Testando coluna: <code>{$colunaTeste}</code></h3>";

            // Verificar se a coluna existe
            $existe = false;
            foreach ($colunasExistentes as $coluna) {
                if ($coluna['Field'] == $colunaTeste) {
                    $existe = true;
                    break;
                }
            }

            if (!$existe) {
                echo "❌ Coluna não existe na tabela<br>";
                continue;
            }

            echo "✅ Coluna existe<br>";

            // Testar JOIN com esta coluna
            try {
                $sql = "SELECT COUNT(*) as total FROM transacoes t 
                       JOIN pessoas p ON p.id = t.{$colunaTeste} 
                       LIMIT 5";
                $query = $db->query($sql);
                $total = $query->getRow()->total;

                echo "Registros relacionados: <strong>{$total}</strong><br>";

                if ($total > 0) {
                    $sql = "SELECT t.id, t.valor, t.tipo, p.nome, p.tipo_cliente 
                           FROM transacoes t 
                           JOIN pessoas p ON p.id = t.{$colunaTeste} 
                           LIMIT 3";
                    $query = $db->query($sql);
                    $dados = $query->getResultArray();

                    echo "<pre>";
                    print_r($dados);
                    echo "</pre>";

                    echo "<strong style='color: green;'>✅ ESTA É PROVAVELMENTE A COLUNA CORRETA!</strong><br>";
                    echo "Para usar esta coluna, adicione no Controller:<br>";
                    echo "<code>\$model->setColunaRelacionamento('{$colunaTeste}');</code>";
                } else {
                    echo "⚠️ Coluna existe mas não há registros relacionados<br>";
                }
            } catch (\Exception $e) {
                echo "❌ Erro no JOIN: " . $e->getMessage() . "<br>";
            }

            echo "<hr>";
        }
    }
}

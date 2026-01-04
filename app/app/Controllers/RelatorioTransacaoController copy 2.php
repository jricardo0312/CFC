<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
// Usando o nome correto do Model que você mencionou anteriormente (TransacaoModel)
// Se o seu Model for realmente RelatorioTransacaoModel, mude a linha abaixo.
use App\Models\TransacaoModel;
use CodeIgniter\Controller;

class RelatorioTransacaoController extends Controller
{
    /**
     * Calcula o primeiro e o último dia do mês corrente.
     * @return array
     */
    private function _getDatasPadrao(): array
    {
        $primeiroDia = date('Y-m-01');
        $ultimoDia   = date('Y-m-t');
        return [$primeiroDia, $ultimoDia];
    }

    /**
     * Exibe a tela do relatório com a seleção de período e a tabela.
     */
    public function index()
    {
        // ATENÇÃO: Verifique se o Model correto é TransacaoModel ou RelatorioTransacaoModel
        $model = new TransacaoModel();
        $request = $this->request;

        // 1. Definição das datas
        [$primeiroDia, $ultimoDia] = $this->_getDatasPadrao();

        $dataInicio = $request->getVar('data_inicio') ?? $primeiroDia;
        $dataFim    = $request->getVar('data_fim') ?? $ultimoDia;

        // 2. Busca dos dados
        $dadosTransacoes = $model->getRelatorio($dataInicio, $dataFim);

        // 3. Preparação dos dados para a View
        $data = [
            'data_inicio' => $dataInicio,
            'data_fim'    => $dataFim,
            'transacoes'  => $dadosTransacoes,
            'title'       => 'Relatório de Transações Financeiras',
            'periodo_txt' => date('d/m/Y', strtotime($dataInicio)) . ' a ' . date('d/m/Y', strtotime($dataFim)),
        ];

        // Se sua view está em 'app/Views/relatorio_transacoes.php', o caminho está correto.
        return view('relatorio_transacoes', $data);
    }

    /**
     * Exporta os dados do relatório em formato CSV.
     */
    public function exportarCsv()
    {
        // ATENÇÃO: Verifique se o Model correto é TransacaoModel ou RelatorioTransacaoModel
        $model = new TransacaoModel();
        $request = $this->request;

        // 1. Definição das datas, priorizando a submissão do formulário
        [$primeiroDia, $ultimoDia] = $this->_getDatasPadrao();

        $dataInicio = $request->getVar('data_inicio') ?? $primeiroDia;
        $dataFim    = $request->getVar('data_fim') ?? $ultimoDia;

        // 2. Busca dos dados
        // Presume que getRelatorio está no Model e retorna o campo 'tipo_fluxo_categoria'
        $dados = $model->getRelatorio($dataInicio, $dataFim);

        if (empty($dados)) {
            return redirect()->back()->with('error', 'Nenhum registro encontrado para o período selecionado.');
        }

        // 3. Configuração do CSV
        $filename = 'relatorio_transacoes_' . date('Ymd_His') . '.csv';

        // Define os cabeçalhos para download
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Abre o buffer de saída
        $output = fopen('php://output', 'w');

        // Adiciona a BOM para garantir a compatibilidade de caracteres especiais no Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Títulos das colunas - ALTERADO: 'Pessoa' foi substituído
        $headers = [
            'ID',
            'Tipo',
            'Valor',
            'Vencimento',
            'Caixa (DFC)',
            'Status',
            'Descrição',
            'Categoria',
            'Tipo de Fluxo da Categoria', // NOVO CABEÇALHO
            'Criado Em',
            'Atualizado Em'
        ];
        fputcsv($output, $headers, ';'); // Usa ponto e vírgula como separador

        // Dados
        foreach ($dados as $row) {
            $dataRow = [
                $row['id'],
                $row['tipo'],
                str_replace('.', ',', $row['valor']), // Formatação de valor com vírgula
                date('d/m/Y', strtotime($row['data_vencimento'])),
                $row['data_caixa'] ? date('d/m/Y', strtotime($row['data_caixa'])) : 'N/A',
                $row['status'],
                $row['descricao'],
                $row['categoria_nome'] ?? 'N/A',
                $row['tipo_fluxo_categoria'] ?? 'N/A', // NOVO CAMPO DE DADOS
                date('d/m/Y H:i:s', strtotime($row['created_at'])),
                date('d/m/Y H:i:s', strtotime($row['updated_at'])),
            ];
            fputcsv($output, $dataRow, ';');
        }

        fclose($output);
        exit;
    }
}

# CFC

*** ATENÇÃO: O SISTEMA ESTÁ EM VERSÃO BETA, SUJEITO A DIVERSOS ERROS IMPREVISÍVEIS  E FALHAS DE SEGURANÇA ***
*** O USO É MERAMENTE PARA ESTUDO   *** NÃO DEVE SER COLOCADO EM PRODUÇÃO!!! ***

💰 Sistema DFC para Clínica de Psicologia (Método Direto)

Este é um projeto de sistema de gestão financeira focado na Demonstração dos Fluxos de Caixa (DFC) pelo Método Direto, desenvolvido em CodeIgniter 4. O objetivo é fornecer à clínica uma visão clara e contábil de suas movimentações de caixa, separando as atividades em Operacionais (FCO), de Investimento (FCI) e de Financiamento (FCF).

🚀 Conceito Principal: DFC e Regimes Financeiros
O sistema opera em dois regimes para garantir a precisão contábil:
Regime de Competência (Contas Pendentes): Registra receitas e despesas quando são contratadas (no lançamento, com status PENDENTE).
Regime de Caixa (DFC): O relatório final só considera transações que foram Liquidadas (ou seja, quando o dinheiro efetivamente entrou ou saiu da conta). A liquidação registra a data_caixa, que é a base para o cálculo da DFC.

Classificações DFC (O Coração do Sistema)

Todas as transações são mapeadas para um dos três tipos de fluxo:
Fluxo
Descrição
Exemplo no Sistema

FCO
Fluxo de Caixa Operacional. Atividades principais do negócio.
Receita de Consultas, Pagamento de Aluguel, Salários.

FCI
Fluxo de Caixa de Investimento. Compra/Venda de Ativos de Longo Prazo.
Compra de Mobiliário, Aquisição de Softwares.

FCF
Fluxo de Caixa de Financiamento. Alterações na estrutura de capital e dívidas.
Empréstimos, Aporte de Capital, Distribuição de Lucros.

🛠️ Módulos Principais

O projeto é estruturado em três grandes módulos que trabalham em conjunto:
Cadastro de Entidades (/pessoas): Gerencia Clientes, Fornecedores e Sócios. (Model: PessoaModel)
Mapeamento DFC (/categorias): Permite o cadastro de categorias de receitas/despesas e o mapeamento obrigatório para FCO, FCI ou FCF. (Model: CategoriasFinanceirasModel)
Módulo Financeiro (/financeiro):
Lançamento: Cria Contas a Pagar/Receber (PENDENTE).
Liquidação: Confirma o movimento de caixa e muda o status para CONCLUÍDA.
Relatório DFC: Gera a Demonstração consolidada do fluxo de caixa no período filtrado.

🤖 Participação da Inteligência Artificial
Este projeto foi desenvolvido em colaboração interativa com o modelo de linguagem Gemini, atuando como um Engenheiro de Software Sênior.
Sua participação incluiu:
Arquitetura: Definição da estrutura MVC e do fluxo lógico da DFC (Lançamento -> Liquidação -> Relatório).
Code Generation: Criação e refatoração de alguns Controllers, Models e Views (HTML/PHP com Tailwind CSS).
Debugging e Manutenção: Diagnóstico e correção de erros cruciais e a correção da sintaxe de classes e views corrompidas durante o processo de comunicação.
Documentação: Elaboração parcial do Manual do Usuário e do README.md.

⚙️ Setup e Instalação

Pré-requisitos
PHP 8.1+
Composer
Banco de Dados MySQL/MariaDB (ou similar)
Passos para Configuração

Clone o Repositório:
git clone (https://github.com/jricardo0312/CFC)
cd CFC


Instale as Dependências (CodeIgniter):
composer install
Configuração do Banco de Dados:
Crie um arquivo .env a partir do env.
Configure as credenciais do seu banco de dados (Ex: database.default.hostname, database.default.database, etc.).
Execute as Migrations:
Isso criará as tabelas pessoas, categorias_financeiras, e transacoes no seu banco, e corrigirá a FOREIGN KEY da transação.
php spark migrate

Execute os Seeders (Dados Iniciais):
Isso adiciona categorias DFC pré-configuradas (FCO/FCI/FCF).
php spark db:seed CategoriaSeeder

Inicie o Servidor Local:
php spark serve

Acesse http://localhost:8080/ para iniciar o Dashboard.

💻 Fluxo de Uso do Sistema

Para testar o DFC, siga estes passos:
Cadastro Inicial: Cadastre pelo menos uma Pessoa (/pessoas) e categorize pelo menos duas categorias (/categorias): uma FCO de Receita e uma FCO de Despesa.
Lançamento: Vá em "Lançamento de Transações" (/financeiro/novo) e registre duas transações PENDENTES.
Liquidação: Vá em "Contas Pendentes" (/financeiro) e clique em "Dar Baixa" em ambas as transações.
Relatório: Acesse "Demonstração dos Fluxos de Caixa (DFC)" (/financeiro/dfc), selecione o período atual e gere o relatório. O resultado deve refletir a diferença entre as transações liquidadas em FCO.
Desenvolvido com o apoio de IA, CodeIgniter 4 e Tailwind CSS.

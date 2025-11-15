<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CorrigirForeignKeyPessoaTransacao extends Migration
{
    /**
     * Esta migration corrige a chave estrangeira da tabela 'transacoes',
     * movendo-a de 'clientes_fornecedores' para a tabela correta 'pessoas'.
     */
    public function up()
    {
        // Garante que estamos usando o DB (necessário para queries manuais)
        $this->db = \Config\Database::connect();

        // Passo 1: Excluir (DROP) a constraint antiga (errada)
        // O nome da constraint é 'transacoes_pessoa_id_foreign' (visto no erro)
        try {
            $this->db->query("ALTER TABLE transacoes DROP FOREIGN KEY transacoes_pessoa_id_foreign");
            // Se houver um índice associado, ele também pode precisar ser removido,
            // mas geralmente o DROP FOREIGN KEY é suficiente.
        } catch (\Exception $e) {
            // Se a constraint não existir (talvez já foi corrigida?),
            // apenas loga e continua, mas não falha.
            log_message('error', 'Falha ao dropar FK antiga: ' . $e->getMessage());
        }

        // Passo 2: Adicionar (ADD) a constraint nova (correta)
        // Ligando transacoes.pessoa_id -> pessoas.id
        $this->db->query("
            ALTER TABLE transacoes 
            ADD CONSTRAINT transacoes_pessoa_id_fk_pessoas 
            FOREIGN KEY (pessoa_id) REFERENCES pessoas(id)
            ON DELETE RESTRICT ON UPDATE CASCADE;
        ");
    }

    /**
     * Reverte a alteração (Rollback)
     */
    public function down()
    {
        $this->db = \Config\Database::connect();

        // Passo 1: Remove a constraint correta
        try {
            $this->db->query("ALTER TABLE transacoes DROP FOREIGN KEY transacoes_pessoa_id_fk_pessoas");
        } catch (\Exception $e) {
            log_message('error', 'Falha ao dropar FK nova: ' . $e->getMessage());
        }

        // Passo 2: Readiciona a constraint antiga (errada)
        try {
            $this->db->query("
                ALTER TABLE transacoes 
                ADD CONSTRAINT transacoes_pessoa_id_foreign 
                FOREIGN KEY (pessoa_id) REFERENCES clientes_fornecedores(id);
            ");
        } catch (\Exception $e) {
            log_message('error', 'Falha ao re-adicionar FK antiga: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTabelaPessoas extends Migration
{
    public function up()
    {
        // Define as colunas da tabela 'pessoas'
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true, // Garante que o email seja único
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
        ]);

        // Define a chave primária
        $this->forge->addKey('id', true);

        // Cria a tabela
        $this->forge->createTable('pessoas');
    }

    public function down()
    {
        // Remove a tabela em caso de rollback
        $this->forge->dropTable('pessoas');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CategoriasFinanceiras extends Migration
{
    public function up()
    {
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
            ],
            // Campo crucial para a DFC: mapeia a categoria ao Fluxo de Caixa
            'tipo_fluxo' => [
                'type'       => 'ENUM',
                'constraint' => ['FCO', 'FCI', 'FCF'],
                'default'    => 'FCO',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('categorias_financeiras');
    }

    public function down()
    {
        $this->forge->dropTable('categorias_financeiras');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocumentoToPessoas extends Migration
{
    public function up()
    {
        // Define os novos campos
        $fields = [
            'tipo_documento' => [
                'type'       => 'ENUM',
                'constraint' => ['CPF', 'CNPJ'],
                'null'       => false,
                'after'      => 'email', // Coloca depois da coluna 'email'
            ],
            'documento' => [
                'type'       => 'VARCHAR',
                'constraint' => '14', // 14 dígitos para CNPJ
                'null'       => true,
                'unique'     => true, // Garante que o documento seja único
                'after'      => 'tipo_documento',
            ],
        ];

        // Adiciona as colunas à tabela 'pessoas'
        $this->forge->addColumn('pessoas', $fields);
    }

    public function down()
    {
        // Remove as colunas (rollback)
        $this->forge->dropColumn('pessoas', ['tipo_documento', 'documento']);
    }
}

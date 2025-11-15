<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ClientesFornecedores extends Migration
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
                'constraint' => '150',
            ],
            // Tipo de Pessoa: C = Cliente, F = Fornecedor
            'tipo_pessoa' => [
                'type'       => 'ENUM',
                'constraint' => ['CLIENTE', 'FORNECEDOR', 'SOCIO'],
            ],
            'cpf_cnpj' => [
                'type'       => 'VARCHAR',
                'constraint' => '18',
                'null'       => true,
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
        $this->forge->createTable('clientes_fornecedores');
    }

    public function down()
    {
        $this->forge->dropTable('clientes_fornecedores');
    }
}

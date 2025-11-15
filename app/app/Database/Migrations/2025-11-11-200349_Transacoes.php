<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transacoes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 9,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Tipo: PAGAR (Saída) ou RECEBER (Entrada)
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['PAGAR', 'RECEBER'],
            ],
            'valor' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'data_vencimento' => [
                'type' => 'DATE',
            ],
            // Data crucial para o DFC: data do movimento de caixa real
            'data_caixa' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['PENDENTE', 'CONCLUIDA'],
                'default'    => 'PENDENTE',
            ],
            'descricao' => [
                'type' => 'TEXT',
            ],
            'categoria_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'pessoa_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
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
        $this->forge->addForeignKey('categoria_id', 'categorias_financeiras', 'id');
        $this->forge->addForeignKey('pessoa_id', 'clientes_fornecedores', 'id');
        $this->forge->createTable('transacoes');
    }

    public function down()
    {
        $this->forge->dropTable('transacoes');
    }
}

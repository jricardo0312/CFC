<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            // --- FCO (Atividades Operacionais) ---
            [
                'nome' => 'Receita - Serviços de Psicologia',
                'tipo_fluxo' => 'FCO',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
            [
                'nome' => 'Receita - Aluguel de Consultórios (Horas)',
                'tipo_fluxo' => 'FCO',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
            [
                'nome' => 'Despesa - Aluguel do Espaço Principal',
                'tipo_fluxo' => 'FCO',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
            [
                'nome' => 'Despesa - Impostos (DAS Simples Nacional)',
                'tipo_fluxo' => 'FCO',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
            [
                'nome' => 'Despesa - Contas de Consumo (Luz, Água, Internet)',
                'tipo_fluxo' => 'FCO',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
            [
                'nome' => 'Despesa - Pró-labore dos Sócios (Operacional)',
                'tipo_fluxo' => 'FCO',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],

            // --- FCI (Atividades de Investimento) ---
            [
                'nome' => 'Investimento - Aquisição de Imobilizado (Móveis/Equipamentos)',
                'tipo_fluxo' => 'FCI',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],

            // --- FCF (Atividades de Financiamento) ---
            [
                'nome' => 'Financiamento - Aporte de Capital dos Sócios',
                'tipo_fluxo' => 'FCF',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
            [
                'nome' => 'Financiamento - Empréstimos Tomados',
                'tipo_fluxo' => 'FCF',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
            [
                'nome' => 'Financiamento - Distribuição de Lucros/Dividendos',
                'tipo_fluxo' => 'FCF',
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ],
        ];

        // Insere os dados na tabela
        $this->db->table('categorias_financeiras')->insertBatch($categorias);
    }
}

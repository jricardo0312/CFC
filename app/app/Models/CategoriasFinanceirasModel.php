<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriasFinanceirasModel extends Model
{
    // Nome da tabela no banco de dados
    protected $table            = 'categorias_financeiras';

    // Chave primária
    protected $primaryKey       = 'id';

    // Usar auto-incremento
    protected $useAutoIncrement = true;

    // Tipo de retorno (array ou objeto)
    protected $returnType       = 'array';

    // Não usar soft deletes (exclusão lógica)
    protected $useSoftDeletes   = false;

    // Campos que o método save() (INSERT/UPDATE) pode preencher
    // Se um campo do formulário não estiver aqui, ele será ignorado.
    protected $allowedFields    = [
        'nome',
        'tipo_fluxo' // FCO, FCI ou FCF
    ];

    // Habilitar timestamps (created_at e updated_at)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // --------------------------------------------------------------------
    // Regras de Validação (Usadas pelo Controller)
    // --------------------------------------------------------------------
    protected $validationRules = [
        // 'id' é necessário para a regra 'is_unique' funcionar na edição
        'id'    => 'permit_empty|is_natural_no_zero',

        'nome'  => 'required|min_length[3]|max_length[100]|is_unique[categorias_financeiras.nome,id,{id}]',

        // Regra crucial para a DFC:
        'tipo_fluxo' => 'required|in_list[FCO,FCI,FCF]',
    ];

    // Mensagens de Erro Personalizadas
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome da Categoria é obrigatório.',
            'min_length' => 'O Nome deve ter pelo menos 3 caracteres.',
            'is_unique' => 'Este Nome de Categoria já está cadastrado.',
        ],
        'tipo_fluxo' => [
            'required' => 'O Tipo de Fluxo (FCO, FCI, FCF) é obrigatório.',
            'in_list' => 'O Tipo de Fluxo deve ser FCO, FCI ou FCF.',
        ],
    ];
}

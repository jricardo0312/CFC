<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientesFornecedoresModel extends Model
{
    protected $table = 'clientes_fornecedores';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'tipo_pessoa', 'cpf_cnpj'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Regras de validação
    protected $validationRules = [
        'nome'        => 'required|max_length[150]',
        // C = Cliente, F = Fornecedor, S = Sócio
        'tipo_pessoa' => 'required|in_list[CLIENTE,FORNECEDOR,SOCIO]',
    ];
}

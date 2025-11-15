<?php

namespace App\Models;

use CodeIgniter\Model;

class PessoaModel extends Model
{
    protected $table            = 'pessoas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Campos permitidos
    protected $allowedFields    = [
        'nome',
        'email',
        'tipo_documento',
        'documento'
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Regras de Validação (ATUALIZADAS PARA EDIÇÃO)
    // O placeholder {id} será substituído pelo ID do registro atual
    protected $validationRules = [
        // O ID é necessário para a regra is_unique na atualização
        'id'    => 'permit_empty|is_natural_no_zero',

        'nome'  => 'required|min_length[3]|max_length[100]',

        // Ex: is_unique[tabela.campo,campo_a_ignorar,valor_a_ignorar]
        'email' => 'required|valid_email|is_unique[pessoas.email,id,{id}]',

        'tipo_documento' => 'required|in_list[CPF,CNPJ]',

        'documento' => 'required|min_length[11]|max_length[14]|is_unique[pessoas.documento,id,{id}]'
    ];

    // Mensagens de Erro
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório.',
            'min_length' => 'O Nome deve ter pelo menos 3 caracteres.',
        ],
        'email' => [
            'required' => 'O campo E-mail é obrigatório.',
            'valid_email' => 'Por favor, insira um E-mail válido.',
            'is_unique' => 'Este E-mail já está cadastrado em outro registro.',
        ],
        'tipo_documento' => [
            'required' => 'Selecione o Tipo de Documento (CPF ou CNPJ).',
            'in_list' => 'Tipo de Documento inválido.',
        ],
        'documento' => [
            'required' => 'O campo Documento é obrigatório.',
            'min_length' => 'O Documento deve ter no mínimo 11 dígitos (CPF).',
            'max_length' => 'O Documento deve ter no máximo 14 dígitos (CNPJ).',
            'is_unique' => 'Este Documento (CPF/CNPJ) já está cadastrado em outro registro.',
        ],
    ];
}

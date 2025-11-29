<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    // Define o nome da tabela no banco de dados que este Model irá manipular.
    protected $table = 'usuarios';

    // Define a chave primária da tabela.
    protected $primaryKey = 'id';

    // Indica que o CodeIgniter deve usar o recurso de Soft Deletes (não estamos usando neste exemplo, mas é útil manter).
    protected $useSoftDeletes = false;

    // Lista dos campos da tabela que podem ser preenchidos (permitidos) via métodos como insert() ou update().
    protected $allowedFields = ['nome', 'email', 'senha'];

    // Indica se os timestamps (created_at, updated_at) devem ser gerados/atualizados automaticamente.
    // Usamos 'criado_em' no banco, então desativamos e tratamos manualmente se necessário, ou configuramos o nome.
    protected $useTimestamps = false;

    // Define métodos de callback para serem executados antes de inserir ou atualizar dados.
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Função de callback para criptografar a senha (hashing).
     * @param array $data O array de dados que será inserido/atualizado.
     * @return array Os dados modificados com a senha hasheada.
     */
    protected function hashPassword(array $data)
    {
        // Verifica se o campo 'senha' existe nos dados sendo processados.
        if (isset($data['data']['senha'])) {
            // Usa a função password_hash() do PHP para criar um hash seguro da senha.
            $data['data']['senha'] = password_hash($data['data']['senha'], PASSWORD_DEFAULT);
        }

        return $data; // Retorna o array de dados com a senha criptografada.
    }
}

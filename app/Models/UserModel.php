<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'tblusers';
    protected $primaryKey    = 'user_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'first_name', 'last_name', 'username', 'password', 'role', 'status',
    ];

    protected $validationRules = [
        'first_name' => 'required|max_length[100]',
        'last_name'  => 'required|max_length[100]',
        'username'   => 'required|min_length[3]|max_length[50]',
        'role'       => 'required|in_list[ADMIN,WRITER,EDITOR]',
    ];

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $q = $this->where('username', $username);
        if ($excludeId) {
            $q->where('user_id !=', $excludeId);
        }
        return $q->countAllResults() > 0;
    }
}
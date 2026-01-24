<?php

namespace Src;

require 'DB.php';

class User
{

    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function getUsers(): array
    {
        return $this->db->select('SELECT * FROM users');
    }

    public function getUser(int $id): array
    {
        return $this->db->select('SELECT * FROM users WHERE `id` = :id', [':id' => $id]);
    }

    public function saveUser(?string $name = null, ?string $password = null): array|bool
    {

        if (count($this->db->select('SELECT * FROM users WHERE `name` = :name AND `password` = :password', [':name' => $name, ':password' => $password])) > 0) {
            return false;
        }

        $this->db->query(
            "INSERT INTO users (`name`, `password`) VALUES (:name, :password)",
            [':name' => $name, ':password' => $password]);

        return $this->db->select('SELECT * FROM users WHERE `name` = :name AND `password` = :password', [':name' => $name, ':password' => $password]);
    }

    public function modifyUser(?int $id = null, ?string $name = null, ?string $password = null): array|bool
    {
        $this->db->query(
            'UPDATE users SET `name` = :name, `password` = :password WHERE `id` = :id',
            [':name' => $name, ':password' => $password, ':id' => $id]);
        return $this->db->select('SELECT * FROM users WHERE `id` = :id', [':id' => $id]);
    }

    public function partialModifyUser(?int $id = null, ?string $name = null, ?string $password = null): array|false
    {

        $fields = ['id', 'name', 'password'];
        $queryFields = [];
        $params = [];

        foreach ($fields as $field) {
            if (!isset($$field)) {
                continue;
            }

            $queryFields[] = $field . ' = :' . $field;
            $params[':' . $field] = $$field;
        }

        $this->db->query('UPDATE users SET ' . implode(',', $queryFields) . ' WHERE `id` = :id',
            $params);

        return $this->db->select('SELECT * FROM users WHERE `id` = :id', [':id' => $id]);
    }

    public function deleteUser(?int $id = null): array|false
    {

        $user = $this->db->select('SELECT * FROM users WHERE `id` = :id', [':id' => $id]);

        if ($this->db->query('DELETE FROM users WHERE `id` = :id', [':id' => $id])) {
            return ['message' => 'User deleted successfully', 'user' => $user];
        }

        return false;

    }

}
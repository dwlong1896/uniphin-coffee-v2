<?php

class UserModel extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): array|null
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE ID = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }
}
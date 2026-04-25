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
        $user = $result->fetch_assoc();
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

    public function createCustomer(array $data): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO users (first_name, last_name, email, phone, password, role, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');

        $role = 'customer';
        $status = 'active';

        $stmt->bind_param(
            'sssssss',
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'],
            $data['password'],
            $role,
            $status
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function updateProfile(int $userId, array $data, ?string $imagePath = null): bool
    {
        if ($imagePath !== null) {
            $stmt = $this->db->prepare('
                UPDATE users
                SET first_name=?, last_name=?, email=?, phone=?, address=?, gender=?, birth_date=?, image=?
                WHERE ID=?
            ');
            $stmt->bind_param(
                'ssssssssi',
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'],
                $data['address'],
                $data['gender'],
                $data['birth_date'],
                $imagePath,
                $userId
            );
        } else {
            $stmt = $this->db->prepare('
                UPDATE users
                SET first_name=?, last_name=?, email=?, phone=?, address=?, gender=?, birth_date=?
                WHERE ID=?
            ');
            $stmt->bind_param(
                'sssssssi',
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'],
                $data['address'],
                $data['gender'],
                $data['birth_date'],
                $userId
            );
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

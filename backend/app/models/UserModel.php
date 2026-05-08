<?php

class UserModel extends Model
{
    protected string $table = 'users';

    public function getAdminUsers(array $filters = []): array
    {
        $conditions = [];
        $params = [];
        $types = '';

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        $role = trim((string) ($filters['role'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));

        if ($keyword !== '') {
            $conditions[] = '
                (
                    CONCAT_WS(" ", COALESCE(first_name, ""), COALESCE(last_name, "")) LIKE ?
                    OR COALESCE(email, "") LIKE ?
                    OR COALESCE(phone, "") LIKE ?
                )
            ';
            $likeKeyword = '%' . $keyword . '%';
            $types .= 'sss';
            array_push($params, $likeKeyword, $likeKeyword, $likeKeyword);
        }

        if ($status !== '') {
            $conditions[] = 'status = ?';
            $types .= 's';
            $params[] = $status;
        }

        if ($role !== '') {
            $conditions[] = 'role = ?';
            $types .= 's';
            $params[] = $role;
        }

        $whereSql = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $sql = "
            SELECT *
            FROM users
            $whereSql
            ORDER BY updated_at DESC, created_at DESC, ID DESC
        ";

        $stmt = $this->db->prepare($sql);

        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $users;
    }

    public function findByEmail(string $email): ?array
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

    public function updateAdminControls(int $userId, string $status, ?string $passwordHash = null): bool
    {
        if ($passwordHash !== null) {
            $stmt = $this->db->prepare('
                UPDATE users
                SET status = ?, password = ?
                WHERE ID = ?
            ');
            $stmt->bind_param('ssi', $status, $passwordHash, $userId);
        } else {
            $stmt = $this->db->prepare('
                UPDATE users
                SET status = ?
                WHERE ID = ?
            ');
            $stmt->bind_param('si', $status, $userId);
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}

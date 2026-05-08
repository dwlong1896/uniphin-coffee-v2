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

        if ($role !== '') {
            $conditions[] = 'role = ?';
            $types .= 's';
            $params[] = $role;
        }

        if ($status !== '') {
            $conditions[] = 'status = ?';
            $types .= 's';
            $params[] = $status;
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

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare('SELECT ID FROM users WHERE email = ? AND ID != ? LIMIT 1');
            $stmt->bind_param('si', $email, $excludeId);
        } else {
            $stmt = $this->db->prepare('SELECT ID FROM users WHERE email = ? LIMIT 1');
            $stmt->bind_param('s', $email);
        }

        $stmt->execute();
        $exists = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $exists;
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

    public function updateUserByAdmin(int $userId, array $data, ?string $imagePath = null): bool
    {
        if ($imagePath !== null) {
            $stmt = $this->db->prepare('
                UPDATE users
                SET first_name=?, last_name=?, email=?, phone=?, address=?, gender=?, birth_date=?, role=?, status=?, image=?
                WHERE ID=?
            ');
            $stmt->bind_param(
                'ssssssssssi',
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'],
                $data['address'],
                $data['gender'],
                $data['birth_date'],
                $data['role'],
                $data['status'],
                $imagePath,
                $userId
            );
        } else {
            $stmt = $this->db->prepare('
                UPDATE users
                SET first_name=?, last_name=?, email=?, phone=?, address=?, gender=?, birth_date=?, role=?, status=?
                WHERE ID=?
            ');
            $stmt->bind_param(
                'sssssssssi',
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'],
                $data['address'],
                $data['gender'],
                $data['birth_date'],
                $data['role'],
                $data['status'],
                $userId
            );
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getDeletionBlockReason(int $userId, string $role): ?string
    {
        if ($this->existsByColumn('comments', 'User_ID', $userId)) {
            return 'Tài khoản này đã có bình luận, không thể xóa trực tiếp.';
        }

        if ($role === 'customer' && $this->existsByColumn('orders', 'Customer_ID', $userId)) {
            return 'Tài khoản này đã phát sinh đơn hàng, không thể xóa trực tiếp.';
        }

        if ($role === 'admin') {
            if ($this->existsByColumn('news', 'Admin_ID', $userId)) {
                return 'Tài khoản quản trị viên này đã đăng tin tức, không thể xóa trực tiếp.';
            }

            if ($this->existsByColumn('faqs', 'Admin_ID', $userId)) {
                return 'Tài khoản quản trị viên này đã tạo mục hỏi đáp, không thể xóa trực tiếp.';
            }

            if ($this->existsByColumn('relied_contacts', 'Admin_ID', $userId)) {
                return 'Tài khoản quản trị viên này đã phản hồi liên hệ, không thể xóa trực tiếp.';
            }
        }

        return null;
    }

    public function deleteUserByAdmin(int $userId): bool
    {
        $this->db->begin_transaction();

        try {
            $deleteCartItemsStmt = $this->db->prepare('
                DELETE ci
                FROM cart_items ci
                INNER JOIN carts c ON c.ID = ci.Cart_ID
                WHERE c.Customer_ID = ?
            ');
            $deleteCartItemsStmt->bind_param('i', $userId);
            $deleteCartItemsStmt->execute();
            $deleteCartItemsStmt->close();

            $deleteContactsStmt = $this->db->prepare('DELETE FROM made_contacts WHERE Customer_ID = ?');
            $deleteContactsStmt->bind_param('i', $userId);
            $deleteContactsStmt->execute();
            $deleteContactsStmt->close();

            $deleteCartsStmt = $this->db->prepare('DELETE FROM carts WHERE Customer_ID = ?');
            $deleteCartsStmt->bind_param('i', $userId);
            $deleteCartsStmt->execute();
            $deleteCartsStmt->close();

            $deleteUserStmt = $this->db->prepare('DELETE FROM users WHERE ID = ?');
            $deleteUserStmt->bind_param('i', $userId);
            $deleted = $deleteUserStmt->execute();
            $deleteUserStmt->close();

            if (!$deleted) {
                throw new RuntimeException('Không thể xóa tài khoản.');
            }

            $this->db->commit();

            return true;
        } catch (Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }

    private function existsByColumn(string $table, string $column, int $value): bool
    {
        $allowedTables = ['comments', 'orders', 'news', 'faqs', 'relied_contacts'];

        if (!in_array($table, $allowedTables, true)) {
            return false;
        }

        $sql = "SELECT 1 FROM {$table} WHERE {$column} = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $value);
        $stmt->execute();
        $exists = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $exists;
    }
}

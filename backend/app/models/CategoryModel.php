<?php

class CategoryModel extends Model
{
    protected string $table = 'product_categories';

    public function getAllWithProductCount(): array
    {
        $sql = '
            SELECT
                c.ID,
                c.name,
                COUNT(p.ID) AS product_count
            FROM product_categories c
            LEFT JOIN products p ON p.P_Cate_ID = c.ID
            GROUP BY c.ID, c.name
            ORDER BY c.name ASC
        ';

        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT ID, name FROM product_categories WHERE ID = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $category = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $category ?: null;
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare('SELECT ID FROM product_categories WHERE name = ? AND ID != ? LIMIT 1');
            $stmt->bind_param('si', $name, $excludeId);
        } else {
            $stmt = $this->db->prepare('SELECT ID FROM product_categories WHERE name = ? LIMIT 1');
            $stmt->bind_param('s', $name);
        }

        $stmt->execute();
        $exists = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $exists;
    }

    public function create(string $name): int
    {
        $stmt = $this->db->prepare('INSERT INTO product_categories (name) VALUES (?)');
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $insertId = (int) $this->db->insert_id;
        $stmt->close();

        return $insertId;
    }

    public function updateName(int $id, string $name): bool
    {
        $stmt = $this->db->prepare('UPDATE product_categories SET name = ? WHERE ID = ?');
        $stmt->bind_param('si', $name, $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function hasProducts(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM products WHERE P_Cate_ID = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $exists = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $exists;
    }

    public function deleteById(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM product_categories WHERE ID = ?');
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}
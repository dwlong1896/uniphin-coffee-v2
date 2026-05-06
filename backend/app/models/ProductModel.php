<?php

class ProductModel extends Model
{
    protected string $table = 'products';

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE ID = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $product ?: null;
    }

    public function getAll() : array {
        $sql = '
                SELECT 
    p.*, 
    c.name AS category_name
FROM products p
JOIN product_categories c 
    ON p.P_Cate_ID = c.ID
ORDER BY p.updated_at DESC
            ';

        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
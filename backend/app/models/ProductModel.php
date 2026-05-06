<?php

class ProductModel extends Model
{
    protected string $table = 'products';

    // Tìm sản phẩm theo ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('
        SELECT 
            p.*,
            c.name AS category_name
        FROM products p
        LEFT JOIN product_categories c ON p.P_Cate_ID = c.ID
        WHERE p.ID = ?
        LIMIT 1
        ');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $product ?: null;
    }

    // Lấy danh sách tất cả sản phẩm
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

    // Lấy danh sách tất cả danh mục sản phẩm
    public function getCategories(): array
    {
        $sql = 'SELECT ID, name FROM product_categories ORDER BY name ASC';
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Kiểm tra xem danh mục có tồn tại hay không
    public function categoryExists(int $categoryId): bool
    {
        $stmt = $this->db->prepare('SELECT ID FROM product_categories WHERE ID = ? LIMIT 1');
        $stmt->bind_param('i', $categoryId);
        $stmt->execute();

        $exists = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $exists;
    }

    // Cập nhật thông tin sản phẩm
    public function updateProduct(int $id, array $data, ?string $image = null): bool
    {
        if ($image !== null) {
            $stmt = $this->db->prepare('
                UPDATE products
                SET name = ?, description = ?, image = ?, status = ?, price = ?, P_Cate_ID = ?, slug = ?, updated_at = NOW()
                WHERE ID = ?
            ');
            $stmt->bind_param(
                'ssssdisi',
                $data['name'],
                $data['description'],
                $image,
                $data['status'],
                $data['price'],
                $data['P_Cate_ID'],
                $data['slug'],
                $id
            );
        } else {
            $stmt = $this->db->prepare('
                UPDATE products
                SET name = ?, description = ?, status = ?, price = ?, P_Cate_ID = ?, slug = ?, updated_at = NOW()
                WHERE ID = ?
            ');
            $stmt->bind_param(
                'sssdisi',
                $data['name'],
                $data['description'],
                $data['status'],
                $data['price'],
                $data['P_Cate_ID'],
                $data['slug'],
                $id
            );
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}
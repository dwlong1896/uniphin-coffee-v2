<?php

class CartModel extends Model
{
    public function findOrCreateCartIdByCustomerId(int $customerId): ?int
    {
        $stmt = $this->db->prepare('SELECT ID FROM carts WHERE Customer_ID = ? LIMIT 1');
        $stmt->bind_param('i', $customerId);
        $stmt->execute();

        $cart = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($cart && isset($cart['ID'])) {
            return (int) $cart['ID'];
        }

        $insert = $this->db->prepare('INSERT INTO carts (Customer_ID) VALUES (?)');
        $insert->bind_param('i', $customerId);
        $created = $insert->execute();
        $newCartId = $created ? (int) $this->db->insert_id : 0;
        $insert->close();

        return $created && $newCartId > 0 ? $newCartId : null;
    }

    public function addItem(int $customerId, int $productId, int $quantity): bool
    {
        $cartId = $this->findOrCreateCartIdByCustomerId($customerId);

        if ($cartId === null) {
            return false;
        }

        $stmt = $this->db->prepare('
            INSERT INTO cart_items (Cart_ID, Product_ID, quantity)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
        ');
        $stmt->bind_param('iii', $cartId, $productId, $quantity);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getItems(int $customerId): array
    {
        $cartId = $this->findOrCreateCartIdByCustomerId($customerId);

        if ($cartId === null) {
            return [];
        }

        $stmt = $this->db->prepare('
            SELECT
                ci.Product_ID,
                ci.quantity,
                p.name,
                p.image,
                p.price,
                p.status,
                c.Name AS category_name,
                (ci.quantity * p.price) AS subtotal
            FROM cart_items ci
            INNER JOIN products p ON p.ID = ci.Product_ID
            LEFT JOIN product_categories c ON c.ID = p.P_Cate_ID
            WHERE ci.Cart_ID = ?
            ORDER BY p.updated_at DESC, p.name ASC
        ');
        $stmt->bind_param('i', $cartId);
        $stmt->execute();

        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $items;
    }

    public function updateItemQuantity(int $customerId, int $productId, int $quantity): bool
    {
        $cartId = $this->findOrCreateCartIdByCustomerId($customerId);

        if ($cartId === null) {
            return false;
        }

        $stmt = $this->db->prepare('
            UPDATE cart_items
            SET quantity = ?
            WHERE Cart_ID = ? AND Product_ID = ?
        ');
        $stmt->bind_param('iii', $quantity, $cartId, $productId);
        $result = $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $result && $affectedRows >= 0;
    }

    public function removeItem(int $customerId, int $productId): bool
    {
        $cartId = $this->findOrCreateCartIdByCustomerId($customerId);

        if ($cartId === null) {
            return false;
        }

        $stmt = $this->db->prepare('DELETE FROM cart_items WHERE Cart_ID = ? AND Product_ID = ?');
        $stmt->bind_param('ii', $cartId, $productId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function findItem(int $customerId, int $productId): ?array
    {
        $cartId = $this->findOrCreateCartIdByCustomerId($customerId);

        if ($cartId === null) {
            return null;
        }

        $stmt = $this->db->prepare('
            SELECT
                ci.Product_ID,
                ci.quantity,
                p.price,
                (ci.quantity * p.price) AS subtotal
            FROM cart_items ci
            INNER JOIN products p ON p.ID = ci.Product_ID
            WHERE ci.Cart_ID = ? AND ci.Product_ID = ?
            LIMIT 1
        ');
        $stmt->bind_param('ii', $cartId, $productId);
        $stmt->execute();

        $item = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $item ?: null;
    }

    public function countItems(int $customerId): int
    {
        $cartId = $this->findOrCreateCartIdByCustomerId($customerId);

        if ($cartId === null) {
            return 0;
        }

        $stmt = $this->db->prepare('SELECT COALESCE(SUM(quantity), 0) AS total_items FROM cart_items WHERE Cart_ID = ?');
        $stmt->bind_param('i', $cartId);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return isset($row['total_items']) ? (int) $row['total_items'] : 0;
    }
}

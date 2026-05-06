<?php

class OrderModel extends Model
{
    public function getCheckoutItems(int $customerId, array $productIds = []): array
    {
        $cartId = $this->findCartIdByCustomerId($customerId);

        if ($cartId === null) {
            return [];
        }

        $productIds = $this->normalizeProductIds($productIds);
        $params = [$cartId];
        $types = 'i';
        $whereIn = '';

        if ($productIds !== []) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $whereIn = " AND ci.Product_ID IN ($placeholders)";
            $types .= str_repeat('i', count($productIds));
            $params = array_merge($params, $productIds);
        }

        $stmt = $this->db->prepare("
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
            WHERE ci.Cart_ID = ?$whereIn
            ORDER BY p.updated_at DESC, p.name ASC
        ");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $items;
    }

    public function createOrderFromCartItems(int $customerId, array $productIds, array $data): int
    {
        $cartId = $this->findCartIdByCustomerId($customerId);

        if ($cartId === null) {
            return 0;
        }

        $items = $this->getCheckoutItems($customerId, $productIds);

        if ($items === []) {
            return 0;
        }

        $totalPrice = array_reduce(
            $items,
            static fn(float $sum, array $item): float => $sum + (float) ($item['subtotal'] ?? 0),
            0.0
        );

        $productIds = array_map(
            static fn(array $item): int => (int) ($item['Product_ID'] ?? 0),
            $items
        );

        $this->db->begin_transaction();

        try {
            $stmt = $this->db->prepare('
                INSERT INTO orders (
                    Customer_ID,
                    Customer_phone,
                    first_name,
                    last_name,
                    Shipping_address,
                    payment_method,
                    status,
                    total_price
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');

            $status = 'pending';
            $stmt->bind_param(
                'issssssd',
                $customerId,
                $data['phone'],
                $data['first_name'],
                $data['last_name'],
                $data['address'],
                $data['payment_method'],
                $status,
                $totalPrice
            );

            if (!$stmt->execute()) {
                throw new RuntimeException('Khong the tao don hang.');
            }

            $orderId = (int) $this->db->insert_id;
            $stmt->close();

            $orderItemStmt = $this->db->prepare('
                INSERT INTO order_items (Order_ID, Product_ID, quantity, price_at_purchase)
                VALUES (?, ?, ?, ?)
            ');

            foreach ($items as $item) {
                $productId = (int) ($item['Product_ID'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);
                $price = (float) ($item['price'] ?? 0);

                $orderItemStmt->bind_param('iiid', $orderId, $productId, $quantity, $price);

                if (!$orderItemStmt->execute()) {
                    throw new RuntimeException('Khong the luu chi tiet don hang.');
                }
            }

            $orderItemStmt->close();

            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $types = 'i' . str_repeat('i', count($productIds));
            $params = array_merge([$cartId], $productIds);

            $deleteStmt = $this->db->prepare("
                DELETE FROM cart_items
                WHERE Cart_ID = ? AND Product_ID IN ($placeholders)
            ");
            $deleteStmt->bind_param($types, ...$params);

            if (!$deleteStmt->execute()) {
                throw new RuntimeException('Khong the cap nhat gio hang.');
            }

            $deleteStmt->close();
            $this->db->commit();

            return $orderId;
        } catch (Throwable $e) {
            $this->db->rollback();
            return 0;
        }
    }

    private function findCartIdByCustomerId(int $customerId): ?int
    {
        $stmt = $this->db->prepare('SELECT ID FROM carts WHERE Customer_ID = ? LIMIT 1');
        $stmt->bind_param('i', $customerId);
        $stmt->execute();

        $cart = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $cart && isset($cart['ID']) ? (int) $cart['ID'] : null;
    }

    private function normalizeProductIds(array $productIds): array
    {
        $normalized = array_values(array_unique(array_filter(array_map(
            static fn($value): int => (int) $value,
            $productIds
        ), static fn(int $id): bool => $id > 0)));

        return $normalized;
    }
}

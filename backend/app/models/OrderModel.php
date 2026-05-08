<?php

class OrderModel extends Model
{
    public function getAdminOrders(array $filters = []): array
    {
        [$whereSql, $types, $params] = $this->buildAdminFilterQuery($filters);

        $sql = "
            SELECT
                o.*,
                CONCAT_WS(' ', NULLIF(TRIM(o.last_name), ''), NULLIF(TRIM(o.first_name), '')) AS customer_name,
                u.email AS customer_email,
                COALESCE(order_summary.item_count, 0) AS item_count
            FROM orders o
            LEFT JOIN users u ON u.ID = o.Customer_ID
            LEFT JOIN (
                SELECT Order_ID, SUM(quantity) AS item_count
                FROM order_items
                GROUP BY Order_ID
            ) order_summary ON order_summary.Order_ID = o.ID
            $whereSql
            ORDER BY o.created_at DESC, o.ID DESC
        ";

        $stmt = $this->db->prepare($sql);

        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $orders;
    }

    public function getAdminOrderStats(): array
    {
        $result = $this->db->query("
            SELECT
                COUNT(*) AS total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_orders,
                SUM(CASE WHEN status = 'shipping' THEN 1 ELSE 0 END) AS shipping_orders,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) AS paid_orders,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END), 0) AS completed_revenue
            FROM orders
        ");

        $stats = $result ? $result->fetch_assoc() : [];

        return $stats ?: [
            'total_orders' => 0,
            'pending_orders' => 0,
            'shipping_orders' => 0,
            'completed_orders' => 0,
            'paid_orders' => 0,
            'completed_revenue' => 0,
        ];
    }

    public function findAdminOrderById(int $orderId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                o.*,
                CONCAT_WS(' ', NULLIF(TRIM(o.last_name), ''), NULLIF(TRIM(o.first_name), '')) AS customer_name,
                u.email AS customer_email,
                u.address AS account_address,
                COALESCE(order_summary.item_count, 0) AS item_count
            FROM orders o
            LEFT JOIN users u ON u.ID = o.Customer_ID
            LEFT JOIN (
                SELECT Order_ID, SUM(quantity) AS item_count
                FROM order_items
                GROUP BY Order_ID
            ) order_summary ON order_summary.Order_ID = o.ID
            WHERE o.ID = ?
            LIMIT 1
        ");
        $stmt->bind_param('i', $orderId);
        $stmt->execute();

        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $order ?: null;
    }

    public function getOrderItemsByOrderId(int $orderId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                oi.Order_ID,
                oi.Product_ID,
                oi.quantity,
                oi.price_at_purchase,
                p.name,
                p.image,
                p.status,
                p.slug,
                (oi.quantity * oi.price_at_purchase) AS subtotal
            FROM order_items oi
            LEFT JOIN products p ON p.ID = oi.Product_ID
            WHERE oi.Order_ID = ?
            ORDER BY p.name ASC, oi.Product_ID ASC
        ");
        $stmt->bind_param('i', $orderId);
        $stmt->execute();

        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $items;
    }

    public function updateAdminOrder(int $orderId, string $status, string $paymentStatus): bool
    {
        $stmt = $this->db->prepare('
            UPDATE orders
            SET status = ?, payment_status = ?
            WHERE ID = ?
        ');
        $stmt->bind_param('ssi', $status, $paymentStatus, $orderId);
        $updated = $stmt->execute();
        $stmt->close();

        return $updated;
    }

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
                throw new RuntimeException('Không thể tạo đơn hàng.');
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
                    throw new RuntimeException('Không thể lưu chi tiết đơn hàng.');
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
                throw new RuntimeException('Không thể cập nhật giỏ hàng.');
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

    private function buildAdminFilterQuery(array $filters): array
    {
        $conditions = [];
        $params = [];
        $types = '';

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));
        $paymentMethod = trim((string) ($filters['payment_method'] ?? ''));
        $paymentStatus = trim((string) ($filters['payment_status'] ?? ''));
        $startDate = trim((string) ($filters['start_date'] ?? ''));
        $endDate = trim((string) ($filters['end_date'] ?? ''));

        if ($keyword !== '') {
            $likeKeyword = '%' . $keyword . '%';
            $conditions[] = "
                (
                    CAST(o.ID AS CHAR) LIKE ?
                    OR CONCAT_WS(' ', COALESCE(o.last_name, ''), COALESCE(o.first_name, '')) LIKE ?
                    OR COALESCE(o.Customer_phone, '') LIKE ?
                    OR COALESCE(u.email, '') LIKE ?
                )
            ";
            $types .= 'ssss';
            array_push($params, $likeKeyword, $likeKeyword, $likeKeyword, $likeKeyword);
        }

        if ($status !== '') {
            $conditions[] = 'o.status = ?';
            $types .= 's';
            $params[] = $status;
        }

        if ($paymentMethod !== '') {
            $conditions[] = 'o.payment_method = ?';
            $types .= 's';
            $params[] = $paymentMethod;
        }

        if ($paymentStatus !== '') {
            $conditions[] = 'o.payment_status = ?';
            $types .= 's';
            $params[] = $paymentStatus;
        }

        if ($this->isValidDate($startDate)) {
            $conditions[] = 'DATE(o.created_at) >= ?';
            $types .= 's';
            $params[] = $startDate;
        }

        if ($this->isValidDate($endDate)) {
            $conditions[] = 'DATE(o.created_at) <= ?';
            $types .= 's';
            $params[] = $endDate;
        }

        $whereSql = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        return [$whereSql, $types, $params];
    }

    private function isValidDate(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        $date = DateTime::createFromFormat('Y-m-d', $value);

        return $date instanceof DateTime && $date->format('Y-m-d') === $value;
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

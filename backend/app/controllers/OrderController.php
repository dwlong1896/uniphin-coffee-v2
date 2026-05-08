<?php

class OrderController extends Controller
{
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function index(): void
    {
        AuthMiddleware::requireAdmin();

        $filters = [
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'payment_method' => trim((string) ($_GET['payment_method'] ?? '')),
            'payment_status' => trim((string) ($_GET['payment_status'] ?? '')),
            'start_date' => trim((string) ($_GET['start_date'] ?? '')),
            'end_date' => trim((string) ($_GET['end_date'] ?? '')),
        ];

        $this->view('admin/pages/orders', [
            'title' => 'Quản lý đơn hàng',
            'orders' => $this->orderModel->getAdminOrders($filters),
            'orderStats' => $this->orderModel->getAdminOrderStats(),
            'orderFilters' => $filters,
        ], 'admin/layouts/main');
    }

    public function viewDetail(): void
    {
        AuthMiddleware::requireAdmin();

        $orderId = (int) ($_GET['id'] ?? 0);
        $filters = [
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'payment_method' => trim((string) ($_GET['payment_method'] ?? '')),
            'payment_status' => trim((string) ($_GET['payment_status'] ?? '')),
            'start_date' => trim((string) ($_GET['start_date'] ?? '')),
            'end_date' => trim((string) ($_GET['end_date'] ?? '')),
        ];

        if ($orderId <= 0) {
            $this->setFlash('error', 'Đơn hàng không hợp lệ.');
            $this->redirect($this->buildOrdersRedirectUrl(http_build_query($this->cleanFilters($filters))));
        }

        $order = $this->orderModel->findAdminOrderById($orderId);

        if ($order === null) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng.');
            $this->redirect($this->buildOrdersRedirectUrl(http_build_query($this->cleanFilters($filters))));
        }

        $this->view('admin/pages/order-detail', [
            'title' => 'Chi tiết đơn hàng',
            'order' => $order,
            'orderItems' => $this->orderModel->getOrderItemsByOrderId($orderId),
            'orderFilters' => $filters,
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        AuthMiddleware::requireAdmin();

        $orderId = (int) ($_GET['id'] ?? 0);
        $status = trim((string) ($_POST['status'] ?? ''));
        $paymentStatus = trim((string) ($_POST['payment_status'] ?? ''));
        $returnQuery = trim((string) ($_POST['return_query'] ?? ''));

        $allowedStatuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
        $allowedPaymentStatuses = ['unpaid', 'paid', 'refunded'];

        if ($orderId <= 0) {
            $this->setFlash('error', 'Đơn hàng không hợp lệ.');
            $this->redirect($this->buildOrdersRedirectUrl($returnQuery));
        }

        $order = $this->orderModel->findAdminOrderById($orderId);

        if ($order === null) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng.');
            $this->redirect($this->buildOrdersRedirectUrl($returnQuery));
        }

        if (!in_array($status, $allowedStatuses, true)) {
            $this->setFlash('error', 'Trạng thái đơn hàng không hợp lệ.');
            $this->redirect($this->buildOrderDetailRedirectUrl($orderId, $returnQuery));
        }

        if (!in_array($paymentStatus, $allowedPaymentStatuses, true)) {
            $this->setFlash('error', 'Trạng thái thanh toán không hợp lệ.');
            $this->redirect($this->buildOrderDetailRedirectUrl($orderId, $returnQuery));
        }

        if (!$this->orderModel->updateAdminOrder($orderId, $status, $paymentStatus)) {
            $this->setFlash('error', 'Không thể cập nhật đơn hàng. Vui lòng thử lại.');
            $this->redirect($this->buildOrderDetailRedirectUrl($orderId, $returnQuery));
        }

        $this->setFlash('success', 'Cập nhật đơn hàng thành công.');
        $this->redirect($this->buildOrderDetailRedirectUrl($orderId, $returnQuery));
    }

    private function buildOrdersRedirectUrl(string $returnQuery = ''): string
    {
        parse_str($returnQuery, $query);

        $path = 'admin/orders';
        $queryString = http_build_query($query);

        if ($queryString !== '') {
            $path .= '?' . $queryString;
        }

        return $this->baseUrl($path);
    }

    private function buildOrderDetailRedirectUrl(int $orderId, string $returnQuery = ''): string
    {
        parse_str($returnQuery, $query);
        $query['id'] = $orderId;

        $path = 'admin/orders/viewdetail';
        $queryString = http_build_query($query);

        if ($queryString !== '') {
            $path .= '?' . $queryString;
        }

        return $this->baseUrl($path);
    }

    private function cleanFilters(array $filters): array
    {
        return array_filter($filters, static fn($value): bool => $value !== '');
    }
}

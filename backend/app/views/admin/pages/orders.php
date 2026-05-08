<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$orders = is_array($orders ?? null) ? $orders : [];
$orderStats = is_array($orderStats ?? null) ? $orderStats : [];
$orderFilters = is_array($orderFilters ?? null) ? $orderFilters : [];

$filterParams = array_filter([
    'keyword' => (string) ($orderFilters['keyword'] ?? ''),
    'status' => (string) ($orderFilters['status'] ?? ''),
    'payment_method' => (string) ($orderFilters['payment_method'] ?? ''),
    'payment_status' => (string) ($orderFilters['payment_status'] ?? ''),
    'start_date' => (string) ($orderFilters['start_date'] ?? ''),
    'end_date' => (string) ($orderFilters['end_date'] ?? ''),
], static fn($value): bool => $value !== '');

$buildOrdersUrl = static function (array $params = []) use ($toUrl, $filterParams): string {
    $merged = array_merge($filterParams, $params);
    $merged = array_filter($merged, static fn($value): bool => $value !== '' && $value !== null);
    $query = http_build_query($merged);

    return $toUrl('admin/orders' . ($query !== '' ? '?' . $query : ''));
};

$buildOrderDetailUrl = static function (int $orderId) use ($toUrl, $filterParams): string {
    $query = http_build_query(array_merge($filterParams, ['id' => $orderId]));

    return $toUrl('admin/orders/viewdetail' . ($query !== '' ? '?' . $query : ''));
};

$formatCurrency = static function ($amount): string {
    return number_format((float) $amount, 0, ',', '.') . ' đ';
};

$formatDateTime = static function (?string $value): string {
    if ($value === null || trim($value) === '') {
        return '--';
    }

    $timestamp = strtotime($value);

    return $timestamp ? date('d/m/Y H:i', $timestamp) : $value;
};

$orderStatusClasses = [
    'pending' => 'bg-warning text-dark',
    'confirmed' => 'bg-info text-dark',
    'shipping' => 'bg-primary',
    'completed' => 'bg-success',
    'cancelled' => 'bg-danger',
];

$paymentStatusClasses = [
    'unpaid' => 'bg-secondary',
    'paid' => 'bg-success',
    'refunded' => 'bg-dark',
];

$paymentMethodClasses = [
    'COD' => 'bg-light text-dark',
    'Bank_Transfer' => 'bg-primary',
];

$statusLabels = [
    'pending' => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'shipping' => 'Đang giao',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy',
];

$paymentStatusLabels = [
    'unpaid' => 'Chưa thanh toán',
    'paid' => 'Đã thanh toán',
    'refunded' => 'Đã hoàn tiền',
];

$paymentMethodLabels = [
    'COD' => 'COD',
    'Bank_Transfer' => 'Chuyển khoản',
];

$totalOrders = (int) ($orderStats['total_orders'] ?? 0);
$pendingOrders = (int) ($orderStats['pending_orders'] ?? 0);
$shippingOrders = (int) ($orderStats['shipping_orders'] ?? 0);
$completedOrders = (int) ($orderStats['completed_orders'] ?? 0);
$paidOrders = (int) ($orderStats['paid_orders'] ?? 0);
$completedRevenue = (float) ($orderStats['completed_revenue'] ?? 0);
?>

<div class="row">
    <div class="col-12 mt-4">
        <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mt-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Tổng đơn hàng</p>
                        <h3 class="mb-0"><?php echo htmlspecialchars((string) $totalOrders, ENT_QUOTES, 'UTF-8'); ?></h3>
                    </div>
                    <span class="badge bg-primary-subtle text-primary"><i class="ti-shopping-cart"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mt-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Chờ xác nhận</p>
                        <h3 class="mb-0"><?php echo htmlspecialchars((string) $pendingOrders, ENT_QUOTES, 'UTF-8'); ?></h3>
                    </div>
                    <span class="badge bg-warning text-dark"><i class="ti-timer"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mt-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Đang giao</p>
                        <h3 class="mb-0"><?php echo htmlspecialchars((string) $shippingOrders, ENT_QUOTES, 'UTF-8'); ?></h3>
                    </div>
                    <span class="badge bg-primary"><i class="ti-truck"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mt-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Doanh thu hoàn thành</p>
                        <h3 class="mb-1"><?php echo htmlspecialchars($formatCurrency($completedRevenue), ENT_QUOTES, 'UTF-8'); ?></h3>
                        <small class="text-muted">
                            <?php echo htmlspecialchars((string) $completedOrders, ENT_QUOTES, 'UTF-8'); ?> đơn hoàn thành,
                            <?php echo htmlspecialchars((string) $paidOrders, ENT_QUOTES, 'UTF-8'); ?> đơn đã thanh toán
                        </small>
                    </div>
                    <span class="badge bg-success"><i class="ti-money"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">Bộ lọc đơn hàng</h4>
                    <div class="d-flex gap-2 mt-3 mt-sm-0">
                        <a href="<?php echo htmlspecialchars($toUrl('admin/orders'), ENT_QUOTES, 'UTF-8'); ?>"
                            class="btn btn-outline-secondary">Xóa lọc</a>
                    </div>
                </div>

                <form action="<?php echo htmlspecialchars($toUrl('admin/orders'), ENT_QUOTES, 'UTF-8'); ?>" method="get">
                    <div class="d-flex flex-nowrap align-items-end gap-3 overflow-auto pb-2">
                        <div style="min-width: 260px;">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="keyword" class="form-control"
                                placeholder="Mã đơn, tên khách, SĐT, email"
                                value="<?php echo htmlspecialchars((string) ($orderFilters['keyword'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div style="min-width: 170px;">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-control">
                                <option value="">Tất cả</option>
                                <?php foreach ($statusLabels as $statusValue => $statusLabel): ?>
                                <option value="<?php echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (($orderFilters['status'] ?? '') === $statusValue) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="min-width: 190px;">
                            <label class="form-label">Thanh toán</label>
                            <select name="payment_status" class="form-control">
                                <option value="">Tất cả</option>
                                <?php foreach ($paymentStatusLabels as $paymentStatusValue => $paymentStatusLabel): ?>
                                <option value="<?php echo htmlspecialchars($paymentStatusValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (($orderFilters['payment_status'] ?? '') === $paymentStatusValue) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($paymentStatusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="min-width: 170px;">
                            <label class="form-label">Phương thức</label>
                            <select name="payment_method" class="form-control">
                                <option value="">Tất cả</option>
                                <?php foreach ($paymentMethodLabels as $paymentMethodValue => $paymentMethodLabel): ?>
                                <option value="<?php echo htmlspecialchars($paymentMethodValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (($orderFilters['payment_method'] ?? '') === $paymentMethodValue) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($paymentMethodLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="min-width: 150px;">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="start_date" class="form-control"
                                value="<?php echo htmlspecialchars((string) ($orderFilters['start_date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div style="min-width: 150px;">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="end_date" class="form-control"
                                value="<?php echo htmlspecialchars((string) ($orderFilters['end_date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div style="min-width: 150px;">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti-filter"></i> Áp dụng
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">Danh sách đơn hàng</h4>
                    <span class="text-muted">
                        Hiện có <?php echo htmlspecialchars((string) count($orders), ENT_QUOTES, 'UTF-8'); ?> đơn phù hợp bộ lọc
                    </span>
                </div>

                <div class="data-tables">
                    <table id="dataTable" class="text-center">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th class="text-center">Mã đơn</th>
                                <th class="text-center">Khách hàng</th>
                                <th class="text-center">Số món</th>
                                <th class="text-center">Tổng tiền</th>
                                <th class="text-center">Thanh toán</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Ngày đặt</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders !== []): ?>
                            <?php foreach ($orders as $order): ?>
                            <?php
                                $orderId = (int) ($order['ID'] ?? 0);
                                $customerName = trim((string) ($order['customer_name'] ?? ''));
                                if ($customerName === '') {
                                    $customerName = trim((string) (($order['last_name'] ?? '') . ' ' . ($order['first_name'] ?? '')));
                                }

                                $orderStatus = (string) ($order['status'] ?? '');
                                $paymentStatus = (string) ($order['payment_status'] ?? '');
                                $paymentMethod = (string) ($order['payment_method'] ?? '');
                                $orderStatusClass = $orderStatusClasses[$orderStatus] ?? 'bg-secondary';
                                $paymentStatusClass = $paymentStatusClasses[$paymentStatus] ?? 'bg-secondary';
                                $paymentMethodClass = $paymentMethodClasses[$paymentMethod] ?? 'bg-secondary';
                            ?>
                            <tr>
                                <td class="fw-semibold">#<?php echo htmlspecialchars((string) $orderId, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-start">
                                    <div class="fw-semibold">
                                        <?php echo htmlspecialchars($customerName !== '' ? $customerName : 'Khách lẻ', ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars((string) ($order['Customer_phone'] ?? '--'), ENT_QUOTES, 'UTF-8'); ?></small>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars((string) ($order['customer_email'] ?? '--'), ENT_QUOTES, 'UTF-8'); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars((string) ($order['item_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($formatCurrency($order['total_price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <span class="badge <?php echo htmlspecialchars($paymentMethodClass, ENT_QUOTES, 'UTF-8'); ?> mb-1">
                                        <?php echo htmlspecialchars($paymentMethodLabels[$paymentMethod] ?? $paymentMethod, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <br>
                                    <span class="badge <?php echo htmlspecialchars($paymentStatusClass, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($paymentStatusLabels[$paymentStatus] ?? $paymentStatus, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo htmlspecialchars($orderStatusClass, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($statusLabels[$orderStatus] ?? $orderStatus, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($formatDateTime((string) ($order['created_at'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($buildOrderDetailUrl($orderId), ENT_QUOTES, 'UTF-8'); ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="ti-eye"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8">Chưa có đơn hàng nào phù hợp với bộ lọc hiện tại.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$order = is_array($order ?? null) ? $order : [];
$orderItems = is_array($orderItems ?? null) ? $orderItems : [];
$orderFilters = is_array($orderFilters ?? null) ? $orderFilters : [];

$filterParams = array_filter([
    'keyword' => (string) ($orderFilters['keyword'] ?? ''),
    'status' => (string) ($orderFilters['status'] ?? ''),
    'payment_method' => (string) ($orderFilters['payment_method'] ?? ''),
    'payment_status' => (string) ($orderFilters['payment_status'] ?? ''),
    'start_date' => (string) ($orderFilters['start_date'] ?? ''),
    'end_date' => (string) ($orderFilters['end_date'] ?? ''),
], static fn($value): bool => $value !== '');

$returnQuery = http_build_query($filterParams);
$backUrl = $toUrl('admin/orders' . ($returnQuery !== '' ? '?' . $returnQuery : ''));
$orderId = (int) ($order['ID'] ?? 0);

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

$statusClasses = [
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

$orderStatus = (string) ($order['status'] ?? '');
$paymentStatus = (string) ($order['payment_status'] ?? '');
$paymentMethod = (string) ($order['payment_method'] ?? '');
$customerName = trim((string) ($order['customer_name'] ?? ''));

if ($customerName === '') {
    $customerName = trim((string) (($order['last_name'] ?? '') . ' ' . ($order['first_name'] ?? '')));
}
?>

<div class="row mt-4">
    <div class="col-12">
        <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body text-white p-4"
                style="background: linear-gradient(135deg, #1f7a8c, #2c5f8a); border-radius: 0.375rem;">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <h3 class="mb-2">Đơn hàng #<?php echo htmlspecialchars((string) $orderId, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="mb-2" style="opacity:0.9;">
                            Khách hàng: <?php echo htmlspecialchars($customerName !== '' ? $customerName : 'Khách lẻ', ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge <?php echo htmlspecialchars($statusClasses[$orderStatus] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($statusLabels[$orderStatus] ?? $orderStatus, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <span class="badge <?php echo htmlspecialchars($paymentStatusClasses[$paymentStatus] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($paymentStatusLabels[$paymentStatus] ?? $paymentStatus, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <span class="badge <?php echo htmlspecialchars($paymentMethodClasses[$paymentMethod] ?? 'bg-secondary', ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($paymentMethodLabels[$paymentMethod] ?? $paymentMethod, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-light" onclick="window.print()">
                            <i class="ti-printer"></i> In đơn hàng
                        </button>
                        <a href="<?php echo htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">
                            <i class="ti-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title mb-0">Thông tin đơn hàng</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Mã đơn</span>
                        <strong>#<?php echo htmlspecialchars((string) $orderId, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Ngày đặt</span>
                        <strong><?php echo htmlspecialchars($formatDateTime((string) ($order['created_at'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Tổng tiền</span>
                        <strong><?php echo htmlspecialchars($formatCurrency($order['total_price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Phương thức thanh toán</span>
                        <strong><?php echo htmlspecialchars($paymentMethodLabels[$paymentMethod] ?? $paymentMethod, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Trạng thái đơn</span>
                        <strong><?php echo htmlspecialchars($statusLabels[$orderStatus] ?? $orderStatus, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Trạng thái thanh toán</span>
                        <strong><?php echo htmlspecialchars($paymentStatusLabels[$paymentStatus] ?? $paymentStatus, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="header-title mb-0">Thông tin giao hàng</h4>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Khách hàng:</strong> <?php echo htmlspecialchars($customerName !== '' ? $customerName : 'Khách lẻ', ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars((string) ($order['customer_email'] ?? '--'), ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="mb-2"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars((string) ($order['Customer_phone'] ?? '--'), ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="mb-0"><strong>Địa chỉ giao:</strong><br><?php echo nl2br(htmlspecialchars((string) ($order['Shipping_address'] ?? '--'), ENT_QUOTES, 'UTF-8')); ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title mb-0">Cập nhật trạng thái</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($toUrl('admin/orders/update?id=' . $orderId), ENT_QUOTES, 'UTF-8'); ?>" method="post">
                    <input type="hidden" name="return_query" value="<?php echo htmlspecialchars($returnQuery, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái đơn hàng</label>
                            <select name="status" class="form-control">
                                <?php foreach ($statusLabels as $statusValue => $statusLabel): ?>
                                <option value="<?php echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo $orderStatus === $statusValue ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái thanh toán</label>
                            <select name="payment_status" class="form-control">
                                <?php foreach ($paymentStatusLabels as $paymentStatusValue => $paymentStatusLabel): ?>
                                <option value="<?php echo htmlspecialchars($paymentStatusValue, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo $paymentStatus === $paymentStatusValue ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($paymentStatusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phương thức thanh toán</label>
                            <input type="text" class="form-control"
                                value="<?php echo htmlspecialchars($paymentMethodLabels[$paymentMethod] ?? $paymentMethod, ENT_QUOTES, 'UTF-8'); ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tổng tiền</label>
                            <input type="text" class="form-control"
                                value="<?php echo htmlspecialchars($formatCurrency($order['total_price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" disabled>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-save"></i> Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="header-title mb-0">Sản phẩm trong đơn</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Tạm tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orderItems !== []): ?>
                            <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <small class="text-muted d-block">Mã sản phẩm: <?php echo htmlspecialchars((string) ($item['Product_ID'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></small>
                                </td>
                                <td class="text-center"><?php echo htmlspecialchars((string) ($item['quantity'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($formatCurrency($item['price_at_purchase'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-center fw-semibold"><?php echo htmlspecialchars($formatCurrency($item['subtotal'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Đơn hàng này chưa có chi tiết sản phẩm.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Tổng cộng</th>
                                <th class="text-center"><?php echo htmlspecialchars($formatCurrency($order['total_price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

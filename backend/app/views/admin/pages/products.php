<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};
$categories = is_array($categories ?? null) ? $categories : [];
$popupMessage = $flashPopupSuccess ?? $flashPopupError ?? null;
$popupTitle = !empty($flashPopupSuccess) ? 'Thành công' : 'Thông báo';
$popupClass = !empty($flashPopupSuccess) ? 'text-success' : 'text-danger';
?>

<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($flashError)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="d-sm-flex justify-content-between align-items-center">
                    <h4 class="header-title">Danh sách sản phẩm</h4>
                    <div class="d-md-flex justify-content-between align-items-center gap-2">
                        <button class="btn btn-primary mb-3" type="button" data-bs-toggle="modal"
                            data-bs-target="#addProductModal">
                            <i class="fa-solid fa-plus"></i> Thêm sản phẩm
                        </button>
                    </div>
                </div>

                <div class="data-tables">
                    <table id="dataTable" class="text-center">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Tên sản phẩm</th>
                                <th class="text-center">Danh mục</th>
                                <th class="text-center">Mô tả</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Giá</th>
                                <th class="text-center">Cập nhật</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                            <?php
                                $statusValue = (string) ($product['status'] ?? '');
                                $statusClass = match ($statusValue) {
                                    'active' => 'bg-success',
                                    'inactive' => 'bg-secondary',
                                    'out_of_stock' => 'bg-warning',
                                    'archived' => 'bg-danger',
                                    default => 'bg-primary',
                                };
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string) ($product['ID'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td>
                                    <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                        title="<?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="status-p <?php echo htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars((string) ($product['price'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['updated_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center justify-content-center gap-2">
                                        <a href="<?php echo $toUrl('admin/products/viewdetail?id=' . urlencode((string) ($product['ID'] ?? ''))); ?>"
                                            class="text-primary text-decoration-none fw-bold" title="Xem chi tiết">
                                            <i class="ti-eye"></i>
                                        </a>
                                        <form
                                            action="<?php echo $toUrl('admin/products/delete?id=' . urlencode((string) ($product['ID'] ?? ''))); ?>"
                                            method="post" style="display:inline;"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                                            <button type="submit"
                                                class="btn btn-link text-danger text-decoration-none fw-bold p-0 border-0"
                                                title="Xóa">
                                                <i class="ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8">Chưa có sản phẩm nào</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                    <h4 class="header-title mb-0">Danh sách danh mục</h4>
                </div>

                <form action="<?php echo $toUrl('admin/product-categories/create'); ?>" method="post"
                    class="row g-3 align-items-end mb-4">
                    <div class="col-md-9">
                        <label class="form-label">Tên danh mục mới</label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập tên danh mục" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-plus"></i> Thêm danh mục
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table id="dataTable2" class="table align-middle mb-0">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th class="text-center" style="width: 90px;">ID</th>
                                <th class="text-center">Tên Danh Mục</th>
                                <th class="text-center" style="width: 160px;">Số Sản Phẩm</th>
                                <th class="text-center" style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="text-center fw-semibold">
                                    <?php echo htmlspecialchars((string) ($category['ID'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td>
                                    <form
                                        action="<?php echo $toUrl('admin/product-categories/update?id=' . urlencode((string) ($category['ID'] ?? ''))); ?>"
                                        method="post" class="d-flex justify-content-center mb-0">
                                        <div class="input-group" style="max-width: 520px;">
                                            <input type="text" name="name" class="form-control"
                                                value="<?php echo htmlspecialchars((string) ($category['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                                required>
                                            <button type="submit" class="btn btn-primary px-4">Lưu</button>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-center fw-semibold">
                                    <?php echo htmlspecialchars((string) ($category['product_count'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="text-center">
                                    <form
                                        action="<?php echo $toUrl('admin/product-categories/delete?id=' . urlencode((string) ($category['ID'] ?? ''))); ?>"
                                        method="post" class="d-inline-block mb-0"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');">
                                        <button type="submit"
                                            class="btn btn-link text-danger p-0 border-0 text-decoration-none"
                                            title="Xóa danh mục">
                                            <i class="ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Chưa có danh mục nào.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm sản phẩm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" action="<?php echo $toUrl('admin/products/create'); ?>" method="post"
                    enctype="multipart/form-data" novalidate>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" name="name" class="form-control" placeholder="Nhập tên sản phẩm"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại</label>
                            <select name="P_Cate_ID" class="form-control" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $category): ?>
                                <option
                                    value="<?php echo htmlspecialchars((string) ($category['ID'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars((string) ($category['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Nhập mô tả sản phẩm"
                                required></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">-- Chọn status --</option>
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                                <option value="out_of_stock">out_of_stock</option>
                                <option value="archived">archived</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giá</label>
                            <input type="number" step="0.01" name="price" class="form-control"
                                placeholder="Nhập giá sản phẩm" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control" placeholder="Nhập slug sản phẩm"
                                required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Chọn file ảnh</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary" form="addProductForm">Xác nhận thêm</button>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($popupMessage)): ?>
<div class="modal fade" id="deleteResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title <?php echo htmlspecialchars($popupClass, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($popupTitle, ENT_QUOTES, 'UTF-8'); ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <?php echo htmlspecialchars($popupMessage, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var popupElement = document.getElementById('deleteResultModal');
    if (!popupElement || typeof bootstrap === 'undefined') {
        return;
    }

    var popupModal = new bootstrap.Modal(popupElement);
    popupModal.show();
});
</script>
<?php endif; ?>

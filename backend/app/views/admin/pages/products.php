<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};
$assetUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/assets/admin/' . ltrim($path, '/');
};
?>

<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-center">
                    <h4 class="header-title">Danh sach san pham</h4>
                    <div class="d-md-flex justify-content-between align-items-center gap-2">
                        <button class="btn btn-primary mb-3" type="button">
                            <i class="fa-solid fa-plus"></i> Them danh muc
                        </button>

                        <button class="btn btn-primary mb-3" type="button" id="addProductTrigger">
                            <i class="fa-solid fa-plus"></i> Them san pham
                        </button>

                    </div>
                </div>
                <div class="data-tables">
                    <table id="dataTable" class="text-center">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th>ID</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Mô tả</th>
                                <th>Trạng thái</th>
                                <th>Giá</th>
                                <th>Hình ảnh</th>
                                <th>Cập nhật</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string) ($product['ID'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars((string) ($product['price'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <td><?php echo htmlspecialchars($product['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($product['updated_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td>
                                    <a href="<?php echo $toUrl('admin/products/viewdetail?id=' . urlencode((string) ($product['ID'] ?? ''))); ?>"
                                        class="text-primary mr-3" title="Xem chi tiet">
                                        <i class="ti-eye"></i>
                                    </a>
                                    <a href="#" class="text-danger product-delete-trigger" title="Xoa">
                                        <i class="ti-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="11">Chua co san pham nao.</td>
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
                <h5 class="modal-title">Them san pham moi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" novalidate>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Ten san pham</label>
                            <input type="text" name="name" class="form-control" placeholder="Nhap ten san pham"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loai</label>
                            <select name="P_Cate_ID" class="form-control" required>
                                <option value="">-- Chon danh muc --</option>
                                <option value="2">Ca phe</option>
                                <option value="3">Tra sua</option>
                                <option value="4">Tra trai cay</option>
                                <option value="5">Banh ngot</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Mo ta</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Nhap mo ta san pham"
                                required></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">-- Chon status --</option>
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                                <option value="out_of_stock">out_of_stock</option>
                                <option value="archive">archive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gia</label>
                            <input type="number" step="0.01" name="price" class="form-control"
                                placeholder="Nhap gia san pham" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Chon file anh</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huy</button>
                <button type="button" class="btn btn-primary" id="saveProductBtn">Xac nhan them</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thong bao</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Them san pham thanh cong.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Dong</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xac nhan xoa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Ban co chac chan muon xoa san pham nay khong?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Khong</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Co, xoa ngay</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thong bao</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Xoa san pham thanh cong.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Dong</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $assetUrl('js/products.js'); ?>"></script>
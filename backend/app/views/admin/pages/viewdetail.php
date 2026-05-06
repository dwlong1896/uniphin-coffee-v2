<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$productId = htmlspecialchars((string) ($product['id'] ?? ''), ENT_QUOTES, 'UTF-8');
$productName = htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8');
$productDescription = htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8');
$productImage = htmlspecialchars($product['image'] ?? '', ENT_QUOTES, 'UTF-8');
$productStatus = htmlspecialchars($product['status'] ?? '', ENT_QUOTES, 'UTF-8');
$productPrice = htmlspecialchars($product['price'] ?? '', ENT_QUOTES, 'UTF-8');
$productStockQuantity = htmlspecialchars((string) ($product['stock_quantity'] ?? ''), ENT_QUOTES, 'UTF-8');
$productCategoryId = htmlspecialchars((string) ($product['P_Cate_ID'] ?? ''), ENT_QUOTES, 'UTF-8');
$productUpdatedAt = htmlspecialchars($product['updated_at'] ?? '', ENT_QUOTES, 'UTF-8');
$productSlug = htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-white p-4"
                style="background: linear-gradient(135deg, #1f7a8c, #2c5f8a); border-radius: 0.375rem;">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo $productImage; ?>" alt="<?php echo $productName; ?>"
                            style="width:96px;height:96px;border-radius:18px;object-fit:cover;border:3px solid rgba(255,255,255,0.25);" />
                        <div class="ms-lg-4 ms-0 mt-3 mt-lg-0">
                            <h3 class="mb-1"><?php echo $productName; ?></h3>
                            <p class="mb-1" style="opacity:0.9;">Product ID: <?php echo $productId; ?></p>
                            <span class="badge bg-light text-dark"><?php echo $productStatus; ?></span>
                        </div>
                    </div>
                    <div class="mt-3 mt-lg-0">
                        <a href="<?php echo $toUrl('admin/products'); ?>" class="btn btn-light">
                            <i class="ti-arrow-left"></i> Quay lai danh sach
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
                <h4 class="header-title mb-0">Thong tin hien tai</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="<?php echo $productImage; ?>" alt="<?php echo $productName; ?>"
                        style="width:100%;max-width:220px;border-radius:16px;object-fit:cover;">
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-hashtag me-2 text-muted"></i> ID</span>
                        <strong><?php echo $productId; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-box me-2 text-muted"></i> Name</span>
                        <strong><?php echo $productName; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-layer-group me-2 text-muted"></i> P_Cate_ID</span>
                        <strong><?php echo $productCategoryId; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-signal me-2 text-muted"></i> Status</span>
                        <strong><?php echo $productStatus; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-money-bill me-2 text-muted"></i> Price</span>
                        <strong><?php echo $productPrice; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-warehouse me-2 text-muted"></i> Stock Quantity</span>
                        <strong><?php echo $productStockQuantity; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-link me-2 text-muted"></i> Slug</span>
                        <strong><?php echo $productSlug; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-clock-rotate-left me-2 text-muted"></i> Updated At</span>
                        <strong><?php echo $productUpdatedAt; ?></strong>
                    </li>
                </ul>

                <div class="mt-4">
                    <h6 class="mb-2">Description</h6>
                    <p class="mb-0 text-muted"><?php echo $productDescription; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title mb-0">Chinh sua san pham</h4>
            </div>
            <div class="card-body">
                <form action="#" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ID</label>
                            <input type="text" name="id" class="form-control" value="<?php echo $productId; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $productName; ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">P_Cate_ID</label>
                            <input type="number" name="P_Cate_ID" class="form-control"
                                value="<?php echo $productCategoryId; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?php echo $productStatus === 'active' ? 'selected' : ''; ?>>active</option>
                                <option value="inactive" <?php echo $productStatus === 'inactive' ? 'selected' : ''; ?>>inactive</option>
                                <option value="out_of_stock" <?php echo $productStatus === 'out_of_stock' ? 'selected' : ''; ?>>out_of_stock</option>
                                <option value="archive" <?php echo $productStatus === 'archive' ? 'selected' : ''; ?>>archive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control"
                                value="<?php echo $productPrice; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control"
                                value="<?php echo $productStockQuantity; ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control" value="<?php echo $productSlug; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Updated At</label>
                            <input type="text" name="updated_at" class="form-control"
                                value="<?php echo $productUpdatedAt; ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="6"><?php echo $productDescription; ?></textarea>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image URL</label>
                            <input type="text" name="image_url" class="form-control" value="<?php echo $productImage; ?>">
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">Luu thay doi</button>
                        <button type="button" class="btn btn-outline-secondary">Xem truoc</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

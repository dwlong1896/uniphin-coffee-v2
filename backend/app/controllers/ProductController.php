<?php

class ProductController extends Controller
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function viewdetail(): void
    {
        AuthMiddleware::requireAdmin();

        $productId = (int) ($_GET['id'] ?? 0);

        if ($productId <= 0) {
            $this->abort(404, 'Không tìm thấy sản phẩm');
        }

        $product = $this->productModel->findById($productId);

        if (!$product) {
            $this->abort(404, 'Không tìm thấy sản phẩm !');
        }

        $categories = $this->productModel->getCategories();

        $this->view('admin/pages/viewdetail', [
            'title' => 'Chi tiết sản phẩm',
            'product' => $product,
            'categories' => $categories,
        ], 'admin/layouts/main');
    }

    public function menu(): void
    {
        $products = $this->productModel->getPublicProducts();

        $this->view('users/pages/san-pham', [
            'products' => $products,
            'pageTitle' => 'Sản phẩm',
            'pageName' => 'Sản phẩm',
        ], 'users/layouts/main');
    }

    public function index(): void
    {
        AuthMiddleware::requireAdmin();

        $products = $this->productModel->getAll();
        $categories = $this->categoryModel->getAllWithProductCount();

        $this->view('admin/pages/products', [
            'title' => 'Danh sách sản phẩm',
            'products' => $products,
            'categories' => $categories,
        ], 'admin/layouts/main');
    }

    public function createCategory(): void
    {
        AuthMiddleware::requireAdmin();

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            $this->setFlash('error', 'Vui lòng nhập tên danh mục !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($this->categoryModel->nameExists($name)) {
            $this->setFlash('error', 'Tên danh mục đã tồn tại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        try {
            $createdId = $this->categoryModel->create($name);
        } catch (mysqli_sql_exception $e) {
            $this->setFlash('error', 'Thêm danh mục thất bại');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($createdId <= 0) {
            $this->setFlash('error', 'Thêm danh mục thất bại');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $this->setFlash('success', 'Thêm danh mục thành công');
        $this->redirect($this->baseUrl('admin/products'));
    }

    public function updateCategory(): void
    {
        AuthMiddleware::requireAdmin();

        $categoryId = (int) ($_GET['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if ($categoryId <= 0) {
            $this->setFlash('popup_error', 'Danh mục không hợp lệ');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $category = $this->categoryModel->findById($categoryId);
        if (!$category) {
            $this->setFlash('error', 'Danh mục không tồn tại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($name === '') {
            $this->setFlash('error', 'Vui lòng nhập tên danh mục !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($this->categoryModel->nameExists($name, $categoryId)) {
            $this->setFlash('error', 'Tên danh mục đã tồn tại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if (!$this->categoryModel->updateName($categoryId, $name)) {
            $this->setFlash('error', 'Cập nhật danh mục thất bại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $this->setFlash('success', 'Cập nhật danh mục thành công !');
        $this->redirect($this->baseUrl('admin/products'));
    }

    public function deleteCategory(): void
    {
        AuthMiddleware::requireAdmin();

        $categoryId = (int) ($_GET['id'] ?? 0);

        if ($categoryId <= 0) {
            $this->setFlash('error', 'Danh mục không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $category = $this->categoryModel->findById($categoryId);
        if (!$category) {
            $this->setFlash('popup_error', 'Danh mục không tồn tại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($this->categoryModel->hasProducts($categoryId)) {
            $this->setFlash('popup_error', 'Không thể xóa danh mục đang được sử dụng');
            $this->redirect($this->baseUrl('admin/products'));
        }

        try {
            $deleted = $this->categoryModel->deleteById($categoryId);
        } catch (mysqli_sql_exception $e) {
            $this->setFlash('popup_error', 'Xóa danh mục thất bại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if (!$deleted) {
            $this->setFlash('popup_error', 'Xóa danh mục thất bại!');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $this->setFlash('popup_success', 'Xóa danh mục thành công!');
        $this->redirect($this->baseUrl('admin/products'));
    }

    public function create(): void
    {
        AuthMiddleware::requireAdmin();

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => trim($_POST['status'] ?? ''),
            'price' => trim($_POST['price'] ?? ''),
            'P_Cate_ID' => (int) ($_POST['P_Cate_ID'] ?? 0),
            'slug' => trim($_POST['slug'] ?? ''),
        ];

        if ($data['status'] === 'archive') {
            $data['status'] = 'archived';
        }

        if ($data['name'] === '' || $data['slug'] === '' || $data['description'] === '' || $data['price'] === '') {
            $this->setFlash('error', 'Vui lòng điền đầy đủ thông tin sản phẩm !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($data['P_Cate_ID'] <= 0 || !$this->productModel->categoryExists($data['P_Cate_ID'])) {
            $this->setFlash('error', 'Danh mục không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if (!in_array($data['status'], ['active', 'inactive', 'out_of_stock', 'archived'], true)) {
            $this->setFlash('error', 'Trạng thái không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if (!is_numeric($data['price']) || (float) $data['price'] < 0) {
            $this->setFlash('error', 'Giá không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($this->productModel->slugExists($data['slug'])) {
            $this->setFlash('error', 'Slug đã tồn tại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            $this->setFlash('error', 'Vui lòng chọn ảnh sản phẩm !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Upload ảnh thất bại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $tmpFile = $_FILES['image']['tmp_name'];
        $mime = mime_content_type($tmpFile);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (!in_array($mime, $allowedMimes, true)) {
            $this->setFlash('error', 'File ảnh không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $this->setFlash('error', 'Ảnh vượt quá 2MB !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $data['price'] = (float) $data['price'];

        $extensionMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        $uploadDir = ROOT_PATH . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = $extensionMap[$mime];
        $imageName = 'product-new-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetPath = $uploadDir . $imageName;

        if (!move_uploaded_file($tmpFile, $targetPath)) {
            $this->setFlash('error', 'Không thể lưu file ảnh');
            $this->redirect($this->baseUrl('admin/products'));
        }

        try {
            $createdId = $this->productModel->createProduct($data, $imageName);
        } catch (mysqli_sql_exception $e) {
            if (is_file($targetPath)) {
                unlink($targetPath);
            }

            $this->setFlash('error', 'Thêm sản phẩm thất bại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($createdId <= 0) {
            if (is_file($targetPath)) {
                unlink($targetPath);
            }

            $this->setFlash('error', 'Thêm sản phẩm thất bại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $finalImageName = 'product-' . $createdId . '-' . time() . '.' . $extension;
        $finalPath = $uploadDir . $finalImageName;

        if ($finalImageName !== $imageName && rename($targetPath, $finalPath)) {
            $this->productModel->updateProduct($createdId, [
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'],
                'price' => $data['price'],
                'P_Cate_ID' => $data['P_Cate_ID'],
                'slug' => $data['slug'],
            ], $finalImageName);
        }

        $this->setFlash('success', 'Thêm sản phẩm thành công !');
        $this->redirect($this->baseUrl('admin/products'));
    }

    public function update(): void
    {
        AuthMiddleware::requireAdmin();

        $productId = (int) ($_GET['id'] ?? 0);

        if ($productId <= 0) {
            $this->setFlash('popup_error', 'Sản phẩm không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $currentProduct = $this->productModel->findById($productId);

        if (!$currentProduct) {
            $this->setFlash('error', 'Sản phẩm không tồn tại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => trim($_POST['status'] ?? ''),
            'price' => trim($_POST['price'] ?? ''),
            'P_Cate_ID' => (int) ($_POST['P_Cate_ID'] ?? 0),
            'slug' => trim($_POST['slug'] ?? ''),
        ];

        if ($data['status'] === 'archive') {
            $data['status'] = 'archived';
        }

        if ($data['name'] === '' || $data['slug'] === '' || $data['description'] === '' || $data['price'] === '') {
            $this->setFlash('error', 'Vui lòng điền đầy đủ thông tin sản phẩm !');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        if ($data['P_Cate_ID'] <= 0 || !$this->productModel->categoryExists($data['P_Cate_ID'])) {
            $this->setFlash('error', 'Danh mục không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        if (!in_array($data['status'], ['active', 'out_of_stock', 'archived', 'inactive'], true)) {
            $this->setFlash('error', 'Trạng thái không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        if ($this->productModel->slugExists($data['slug'], $productId)) {
            $this->setFlash('error', 'Slug đã tồn tại !');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        if (!is_numeric($data['price']) || (float) $data['price'] < 0) {
            $this->setFlash('error', 'Giá không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        $data['price'] = (float) $data['price'];

        $newImageName = null;
        $uploadDir = ROOT_PATH . '/public/uploads/';

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $this->setFlash('error', 'Upload ảnh thất bại');
                $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
            }

            $tmpFile = $_FILES['image']['tmp_name'];
            $mime = mime_content_type($tmpFile);

            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($mime, $allowedMimes, true)) {
                $this->setFlash('error', 'File ảnh không hợp lệ !');
                $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
            }

            if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $this->setFlash('error', 'Ảnh vượt quá 2MB');
                $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
            }

            $extensionMap = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
            ];

            $extension = $extensionMap[$mime];
            $fileName = 'product-' . $productId . '-' . time() . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            $newImageName = $fileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!move_uploaded_file($tmpFile, $targetPath)) {
                $this->setFlash('error', 'Không thể lưu file ảnh !');
                $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
            }
        }

        $updated = $this->productModel->updateProduct($productId, $data, $newImageName);
        if (!$updated) {
            if ($newImageName !== null) {
                $newImageFullPath = $uploadDir . $newImageName;
                if (is_file($newImageFullPath)) {
                    unlink($newImageFullPath);
                }
            }

            $this->setFlash('error', 'Cập nhật sản phẩm thất bại !');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        if ($newImageName !== null) {
            $oldImage = trim((string) ($currentProduct['image'] ?? ''));

            if ($oldImage !== '' && $oldImage !== $newImageName) {
                $oldImageFullPath = $uploadDir . $oldImage;
                if (is_file($oldImageFullPath)) {
                    unlink($oldImageFullPath);
                }
            }
        }

        $this->setFlash('success', 'Cập nhật sản phẩm thành công !');
        $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
    }

    // Xóa sản phẩm
    public function delete(): void
    {
        AuthMiddleware::requireAdmin();

        $productId = (int) ($_GET['id'] ?? 0);

        if ($productId <= 0) {
            $this->setFlash('error', 'Sản phẩm không hợp lệ !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $currentProduct = $this->productModel->findById($productId);

        if (!$currentProduct) {
            $this->setFlash('popup_error', 'Sản phẩm không tồn tại');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if ($this->productModel->hasOrderItems($productId)) {
            $this->setFlash('popup_error', 'Không thể xóa sản phẩm đã có trong đơn hàng !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        try {
            if (!$this->productModel->deleteCartItemsByProductId($productId)) {
                $this->setFlash('popup_error', 'Xóa sản phẩm thất bại !');
                $this->redirect($this->baseUrl('admin/products'));
            }

            $deleted = $this->productModel->deleteById($productId);
        } catch (mysqli_sql_exception $e) {
            $this->setFlash('popup_error', 'Không thể xóa sản phẩm do dữ liệu liên quan !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        if (!$deleted) {
            $this->setFlash('popup_error', 'Xóa sản phẩm thất bại !');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $imageName = trim((string) ($currentProduct['image'] ?? ''));
        if ($imageName !== '') {
            $imagePath = ROOT_PATH . '/public/uploads/' . $imageName;
            if (is_file($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->setFlash('popup_success', 'Xóa sản phẩm thành công !');
        $this->redirect($this->baseUrl('admin/products'));
    }
}

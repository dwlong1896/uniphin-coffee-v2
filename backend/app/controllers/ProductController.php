<?php

class ProductController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function viewdetail(): void
    {
        AuthMiddleware::requireAdmin();

        $productId = (int) ($_GET['id'] ?? 0);

        if ($productId <= 0) {
            $this->abort(404, 'Product not found');
        }

        $product = $this->productModel->findById($productId);

        if (!$product) {
            $this->abort(404, 'Product not found');
        }

        $categories = $this->productModel->getCategories();

        $this->view('admin/pages/viewdetail', [
            'title' => 'Chi tiet san pham',
            'product' => $product,
            'categories' => $categories,
        ], 'admin/layouts/main');
    }

    public function index(): void
    {
        AuthMiddleware::requireAdmin();

        $products = $this->productModel->getAll();

        $this->view('admin/pages/products', [
            'title' => 'Danh sach san pham',
            'products' => $products,
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        AuthMiddleware::requireAdmin();

        $productId = (int) ($_GET['id'] ?? 0);

        if ($productId <= 0) {
            $this->setFlash('error', 'San pham khong hop le');
            $this->redirect($this->baseUrl('admin/products'));
        }

        $currentProduct = $this->productModel->findById($productId);

        if (!$currentProduct) {
            $this->setFlash('error', 'San pham khong ton tai');
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

        if ($data['name'] === '' || $data['slug'] === '' || $data['description'] === '' || $data['price'] === '') {
            $this->setFlash('error', 'Vui long dien day du thong tin san pham');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        if ($data['P_Cate_ID'] <= 0 || !$this->productModel->categoryExists($data['P_Cate_ID'])) {
            $this->setFlash('error', 'Danh muc khong hop le');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        if (!in_array($data['status'], ['active', 'out_of_stock', 'archive', 'inactive'], true)) {
            $this->setFlash('error', 'Trang thai khong hop le');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        if (!is_numeric($data['price']) || (float) $data['price'] < 0) {
            $this->setFlash('error', 'Gia khong hop le');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        $data['price'] = (float) $data['price'];

        $newImageName = null;
        $uploadDir = ROOT_PATH . '/public/uploads/';

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $this->setFlash('error', 'Upload anh that bai');
                $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
            }

            $tmpFile = $_FILES['image']['tmp_name'];
            $mime = mime_content_type($tmpFile);

            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($mime, $allowedMimes, true)) {
                $this->setFlash('error', 'File anh khong hop le');
                $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
            }

            if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $this->setFlash('error', 'Anh vuot qua 2MB');
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
                $this->setFlash('error', 'Khong the luu file anh');
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

            $this->setFlash('error', 'Cap nhat san pham that bai');
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

        $this->setFlash('success', 'Cap nhat san pham thanh cong');
        $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
    }
}

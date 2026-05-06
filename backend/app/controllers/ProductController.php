<?php

class ProductController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    // Xem chi tiết sản phẩm
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
            'title' => 'Chi tiết sản phẩm',
            'product' => $product,
            'categories' => $categories,
        ], 'admin/layouts/main');
    }

    // Lấy danh sách tất cả sản phẩm
    public function index(): void{
        AuthMiddleware::requireAdmin();

        $products = $this->productModel->getAll();

        $this->view('admin/pages/products', [
            'title' => 'Danh sách sản phẩm',
            'products' => $products,
        ], 'admin/layouts/main');
    }

    // Cập nhật thông tin sản phẩm
    public function update(): void{
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
            'P_Cate_ID' => (int)($_POST['P_Cate_ID'] ?? 0),
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

        if (!in_array($data['status'], ['active','out_of_stock','archive' ,'inactive'], true)) {
            $this->setFlash('error', 'Trang thai khong hop le');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        if (!is_numeric($data['price']) || (float) $data['price'] < 0) {
            $this->setFlash('error', 'Gia khong hop le');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        }

        $data['price'] = (float) $data['price'];

        $updated = $this->productModel->updateProduct($productId, $data);
        if (!$updated) {
            $this->setFlash('error', 'Cap nhat san pham that bai');
            $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
        } 
            
        $this->setFlash('success', 'Cap nhat san pham thanh cong');
        
        $this->redirect($this->baseUrl('admin/products/viewdetail?id=' . $productId));
    }
}
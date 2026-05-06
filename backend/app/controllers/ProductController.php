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

        $this->view('admin/pages/viewdetail', [
            'title' => 'Chi tiết sản phẩm',
            'product' => $product,
        ], 'admin/layouts/main');
    }

    public function index(): void{
        AuthMiddleware::requireAdmin();

        $products = $this->productModel->getAll();

        $this->view('admin/pages/products', [
            'title' => 'Danh sách sản phẩm',
            'products' => $products,
        ], 'admin/layouts/main');
    }
}
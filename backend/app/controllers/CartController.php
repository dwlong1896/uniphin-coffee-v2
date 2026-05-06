<?php

class CartController extends Controller
{
    private CartModel $cartModel;
    private ProductModel $productModel;
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
    }

    public function index(): void
    {
        if (!$this->isLoggedInCustomer()) {
            $this->setFlash('error', 'Vui long dang nhap de xem gio hang.');
            $this->redirect($this->baseUrl('login'));
        }

        $customerId = (int) ($_SESSION['user_id'] ?? 0);
        $cartItems = $this->cartModel->getItems($customerId);
        $cartTotal = array_reduce(
            $cartItems,
            static fn(float $sum, array $item): float => $sum + (float) ($item['subtotal'] ?? 0),
            0.0
        );

        $this->view('users/pages/cart', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'pageTitle' => 'Gio hang',
            'pageName' => 'Gio hang',
        ], 'users/layouts/main');
    }

    public function add(): void
    {
        if (!$this->isLoggedInCustomer()) {
            $this->json([
                'success' => false,
                'message' => 'Vui long dang nhap de them san pham vao gio hang.',
                'redirect_url' => $this->baseUrl('login'),
            ], 401);
            return;
        }

        $customerId = (int) ($_SESSION['user_id'] ?? 0);
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            $this->json([
                'success' => false,
                'message' => 'San pham khong hop le.',
            ], 422);
            return;
        }

        $product = $this->productModel->findById($productId);

        if ($product === null) {
            $this->json([
                'success' => false,
                'message' => 'San pham khong ton tai.',
            ], 404);
            return;
        }

        $productStatus = (string) ($product['status'] ?? '');

        if ($productStatus !== 'active') {
            $this->json([
                'success' => false,
                'message' => 'San pham hien khong the them vao gio hang.',
            ], 422);
            return;
        }

        if (!$this->cartModel->addItem($customerId, $productId, $quantity)) {
            $this->json([
                'success' => false,
                'message' => 'Khong the them san pham vao gio hang.',
            ], 500);
            return;
        }

        $this->json([
            'success' => true,
            'message' => 'Da them san pham vao gio hang.',
        ]);
    }

    public function checkout(): void
    {
        if (!$this->isLoggedInCustomer()) {
            $this->setFlash('error', 'Vui long dang nhap de thanh toan.');
            $this->redirect($this->baseUrl('login'));
        }

        $customerId = (int) ($_SESSION['user_id'] ?? 0);
        $selectedItems = $this->parseSelectedItems($_GET['items'] ?? '');
        $checkoutItems = $this->orderModel->getCheckoutItems($customerId, $selectedItems);

        if ($checkoutItems === []) {
            $this->setFlash('error', 'Khong co san pham hop le de thanh toan.');
            $this->redirect($this->baseUrl('cart'));
        }

        $this->renderCheckoutPage($checkoutItems, [
            'address' => (string) ($_SESSION['address'] ?? ''),
            'full_name' => trim((string) (($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''))),
            'phone' => (string) ($_SESSION['phone'] ?? ''),
            'payment_method' => 'COD',
            'selected_items' => implode(',', array_map(
                static fn(array $item): int => (int) ($item['Product_ID'] ?? 0),
                $checkoutItems
            )),
        ]);
    }

    public function placeOrder(): void
    {
        if (!$this->isLoggedInCustomer()) {
            $this->setFlash('error', 'Vui long dang nhap de thanh toan.');
            $this->redirect($this->baseUrl('login'));
        }

        $customerId = (int) ($_SESSION['user_id'] ?? 0);
        $selectedItems = $this->parseSelectedItems($_POST['selected_items'] ?? '');
        $checkoutItems = $this->orderModel->getCheckoutItems($customerId, $selectedItems);

        if ($checkoutItems === []) {
            $this->setFlash('error', 'Khong co san pham hop le de thanh toan.');
            $this->redirect($this->baseUrl('cart'));
        }

        $formData = [
            'address' => trim((string) ($_POST['address'] ?? '')),
            'full_name' => trim((string) ($_POST['full_name'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'payment_method' => trim((string) ($_POST['payment_method'] ?? 'COD')),
            'selected_items' => implode(',', $selectedItems),
        ];

        if ($formData['address'] === '' || $formData['full_name'] === '' || $formData['phone'] === '') {
            $this->renderCheckoutPage($checkoutItems, $formData, 'Vui long nhap day du thong tin giao hang.');
            return;
        }

        if (!in_array($formData['payment_method'], ['COD', 'Bank_Transfer'], true)) {
            $this->renderCheckoutPage($checkoutItems, $formData, 'Phuong thuc thanh toan khong hop le.');
            return;
        }

        [$firstName, $lastName] = $this->splitFullName($formData['full_name']);

        $orderId = $this->orderModel->createOrderFromCartItems($customerId, $selectedItems, [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $formData['phone'],
            'address' => $formData['address'],
            'payment_method' => $formData['payment_method'],
        ]);

        if ($orderId <= 0) {
            $this->renderCheckoutPage($checkoutItems, $formData, 'Khong the hoan tat don hang. Vui long thu lai.');
            return;
        }

        $_SESSION['phone'] = $formData['phone'];
        $_SESSION['address'] = $formData['address'];
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;
        $_SESSION['name'] = trim($firstName . ' ' . $lastName);

        $this->setFlash('success', 'Dat hang thanh cong. Ma don hang #' . $orderId . '.');
        $this->redirect($this->baseUrl('cart'));
    }

    public function update(): void
    {
        if (!$this->isLoggedInCustomer()) {
            $this->json([
                'success' => false,
                'message' => 'Vui long dang nhap de cap nhat gio hang.',
                'redirect_url' => $this->baseUrl('login'),
            ], 401);
            return;
        }

        $customerId = (int) ($_SESSION['user_id'] ?? 0);
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            $this->json([
                'success' => false,
                'message' => 'San pham khong hop le.',
            ], 422);
            return;
        }

        if (!$this->cartModel->updateItemQuantity($customerId, $productId, $quantity)) {
            $this->json([
                'success' => false,
                'message' => 'Khong the cap nhat so luong.',
            ], 500);
            return;
        }

        $item = $this->cartModel->findItem($customerId, $productId);

        if ($item === null) {
            $this->json([
                'success' => false,
                'message' => 'Khong tim thay san pham trong gio hang.',
            ], 404);
            return;
        }

        $this->json([
            'success' => true,
            'message' => 'Da cap nhat so luong.',
            'quantity' => (int) ($item['quantity'] ?? $quantity),
            'subtotal' => (float) ($item['subtotal'] ?? 0),
        ]);
    }

    public function remove(): void
    {
        if (!$this->isLoggedInCustomer()) {
            $this->json([
                'success' => false,
                'message' => 'Vui long dang nhap de cap nhat gio hang.',
                'redirect_url' => $this->baseUrl('login'),
            ], 401);
            return;
        }

        $customerId = (int) ($_SESSION['user_id'] ?? 0);
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            $this->json([
                'success' => false,
                'message' => 'San pham khong hop le.',
            ], 422);
            return;
        }

        if (!$this->cartModel->removeItem($customerId, $productId)) {
            $this->json([
                'success' => false,
                'message' => 'Khong the xoa san pham khoi gio hang.',
            ], 500);
            return;
        }

        $this->json([
            'success' => true,
            'message' => 'Da xoa san pham khoi gio hang.',
        ]);
    }

    private function isLoggedInCustomer(): bool
    {
        return !empty($_SESSION['user_id']) && (string) ($_SESSION['role'] ?? '') === 'customer';
    }

    private function parseSelectedItems(string $raw): array
    {
        if (trim($raw) === '') {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            static fn(string $value): int => (int) trim($value),
            explode(',', $raw)
        ), static fn(int $id): bool => $id > 0)));
    }

    private function renderCheckoutPage(array $checkoutItems, array $formData, ?string $error = null): void
    {
        $checkoutTotal = array_reduce(
            $checkoutItems,
            static fn(float $sum, array $item): float => $sum + (float) ($item['subtotal'] ?? 0),
            0.0
        );

        $this->view('users/pages/checkout', [
            'checkoutItems' => $checkoutItems,
            'checkoutTotal' => $checkoutTotal,
            'checkoutFormData' => $formData,
            'flashError' => $error,
            'pageTitle' => 'Thanh toan',
            'pageName' => 'Thanh toan',
        ], 'users/layouts/main');
    }

    private function splitFullName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        $firstName = array_pop($parts);
        $lastName = trim(implode(' ', $parts));

        if ($firstName === null || $firstName === '') {
            return [$fullName, ''];
        }

        return [$firstName, $lastName];
    }
}

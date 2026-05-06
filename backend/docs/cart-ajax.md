# AJAX Cart Flow

## Muc tieu

Nut `THEM VAO GIO` tren popup trang `san-pham` gui AJAX toi backend PHP thuan de them san pham vao bang `cart_items`.

## File lien quan

- `backend/app/models/CartModel.php`
- `backend/app/controllers/CartController.php`
- `backend/routes/web.php`
- `backend/public/index.php`
- `backend/app/views/users/pages/san-pham.php`
- `backend/public/assets/js/user/san-pham.js`
- `backend/app/views/users/pages/cart.php`

## Route da them

GET:

- `/cart`
- `/gio-hang`

POST:

- `/cart/add`
- `/gio-hang/them`

## Luong xu ly

1. User mo popup san pham.
2. JS doc `data-product-id` va `quantity` trong popup.
3. Khi bam nut `THEM VAO GIO`, JS goi:

```text
POST /cart/add
Content-Type: application/x-www-form-urlencoded
```

4. `CartController::add()` validate:

- user da dang nhap va role = `customer`
- `product_id` hop le
- san pham ton tai
- san pham dang `active`

Khong kiem tra `stock_quantity` vi day la luong do an/uong lam khi khach dat.

5. `CartModel::addItem()` dung:

```sql
INSERT INTO cart_items (Cart_ID, Product_ID, quantity)
VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
```

6. Backend tra JSON cho frontend.

## Response JSON

Thanh cong:

```json
{
  "success": true,
  "message": "Da them san pham vao gio hang."
}
```

Chua dang nhap:

```json
{
  "success": false,
  "message": "Vui long dang nhap de them san pham vao gio hang.",
  "redirect_url": "/uniphin2/backend/public/login"
}
```

That bai do validate:

```json
{
  "success": false,
  "message": "San pham hien khong the them vao gio hang."
}
```

## Model hien tai

`CartModel` dang co cac method:

- `findOrCreateCartIdByCustomerId(int $customerId): ?int`
- `addItem(int $customerId, int $productId, int $quantity): bool`
- `getItems(int $customerId): array`
- `countItems(int $customerId): int`

## Controller hien tai

`CartController` dang co:

- `index()`: render trang `/cart`
- `add()`: nhan AJAX them vao gio

## Frontend popup

Modal popup trong `san-pham.php` da duoc bo sung:

- `data-cart-add-url`
- `data-login-url`
- `data-cart-url`
- `data-product-id` tren tung item

JS trong `san-pham.js` se:

- cap nhat `product_id` hien tai khi mo popup
- lay `quantity`
- goi `fetch(...)`
- hien feedback thanh cong / that bai ngay trong popup

JS giu o muc toi thieu:

- khong badge cart realtime
- khong dong bo so luong len header
- khong xu ly stock o frontend

## Trang gio hang

`/cart` hien tai doc du lieu tu database, khong con la cart tam trong session.

## Gioi han hien tai

1. `flashsale` dang hien gia giam tren popup, nhung khi them vao gio hang backend van luu theo gia goc trong bang `products`.
2. Chua co API cap nhat / xoa item bang AJAX trong trang cart.

## Buoc tiep theo de mo rong

1. Them nut `+ -` va `xoa` trong trang `/cart`.
2. Tao AJAX cho `/cart/update` va `/cart/remove`.
3. Neu can gia khuyen mai that su, phai them schema discount hoac bang promotion rieng, khong nen chi doi gia o frontend.

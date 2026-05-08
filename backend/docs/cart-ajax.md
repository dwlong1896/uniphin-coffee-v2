# Tài liệu AJAX giỏ hàng

## Mục tiêu

Nút `THÊM VÀO GIỎ` trên popup trang `sản phẩm` gửi AJAX tới backend PHP thuần để thêm sản phẩm vào bảng `cart_items`.

---

## File liên quan

- `backend/app/models/CartModel.php`
- `backend/app/controllers/CartController.php`
- `backend/routes/web.php`
- `backend/public/index.php`
- `backend/app/views/users/pages/san-pham.php`
- `backend/public/assets/js/user/san-pham.js`
- `backend/app/views/users/pages/cart.php`
- `backend/public/assets/js/user/cart.js`

---

## Route đã dùng

### GET

- `/cart`
- `/gio-hang`

### POST

- `/cart/add`
- `/gio-hang/them`
- `/cart/update`
- `/gio-hang/cap-nhat`
- `/cart/remove`
- `/gio-hang/xoa`

---

## Luồng xử lý thêm vào giỏ

1. Người dùng mở popup sản phẩm.
2. JavaScript đọc `data-product-id` và `quantity`.
3. Khi bấm `THÊM VÀO GIỎ`, frontend gọi:

```text
POST /cart/add
Content-Type: application/x-www-form-urlencoded
```

4. `CartController::add()` kiểm tra:
   - người dùng đã đăng nhập
   - role là `customer`
   - `product_id` hợp lệ
   - sản phẩm tồn tại
   - sản phẩm đang ở trạng thái `active`

5. `CartModel::addItem()` ghi dữ liệu vào `cart_items`.
6. Backend trả JSON cho frontend.
7. Frontend hiển thị phản hồi thành công hoặc thất bại trong popup.

---

## JSON response

### Thành công

```json
{
  "success": true,
  "message": "Đã thêm sản phẩm vào giỏ hàng."
}
```

### Chưa đăng nhập

```json
{
  "success": false,
  "message": "Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.",
  "redirect_url": "/uniphin2/backend/public/login"
}
```

### Thất bại do validate

```json
{
  "success": false,
  "message": "Sản phẩm hiện không thể thêm vào giỏ hàng."
}
```

---

## Cập nhật và xóa trong giỏ hàng

Sau khi đã vào trang `/cart`, người dùng có thể:

1. Thay đổi số lượng sản phẩm
2. Xóa sản phẩm khỏi giỏ
3. Chọn các món để thanh toán

JavaScript trong `cart.js` sẽ gọi:

- `POST /cart/update` để cập nhật số lượng
- `POST /cart/remove` để xóa sản phẩm

Nếu session hết hạn:

1. Backend trả `401`
2. Frontend tự chuyển hướng về trang đăng nhập

---

## Model hiện tại

`CartModel` đang có các nhóm method chính:

- tìm hoặc tạo giỏ hàng của khách
- thêm sản phẩm vào giỏ
- lấy danh sách sản phẩm trong giỏ
- cập nhật số lượng
- xóa sản phẩm

---

## Controller hiện tại

`CartController` đang xử lý:

- `index()`: render trang giỏ hàng
- `add()`: thêm vào giỏ bằng AJAX
- `update()`: cập nhật số lượng bằng AJAX
- `remove()`: xóa sản phẩm bằng AJAX
- `checkout()`: mở trang thanh toán
- `placeOrder()`: hoàn tất đặt hàng

---

## Giao diện hiện tại

### Popup sản phẩm

Popup trong `san-pham.php` và `san-pham.js` hiện đã có:

- chọn số lượng
- gửi AJAX thêm vào giỏ
- thông báo lỗi/thành công bằng tiếng Việt có dấu

### Trang giỏ hàng

Trang `cart.php` hiện có:

- chọn tất cả
- chọn từng món
- cập nhật số lượng
- xóa món
- tính lại tổng tiền bằng JavaScript
- nút `THANH TOÁN`

---

## Ghi chú

1. Backend hiện lưu đơn giá theo dữ liệu sản phẩm hiện tại trong lúc thêm giỏ và tạo đơn.
2. Khi checkout, giá chốt đơn sẽ được lưu vào `order_items.price_at_purchase`.
3. Nếu cần hỗ trợ khuyến mãi thực sự, nên bổ sung schema giảm giá riêng thay vì chỉ đổi giá ở frontend.

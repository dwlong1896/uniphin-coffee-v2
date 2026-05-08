# Tài liệu luồng sản phẩm, giỏ hàng và checkout

## 1. Mục tiêu của tài liệu

Tài liệu này mô tả lại toàn bộ luồng mua hàng phía người dùng trong project hiện tại:

1. Trang sản phẩm `san-pham`
2. Giỏ hàng `cart`
3. Thanh toán `checkout`
4. Tạo đơn hàng `orders`

Mục tiêu:

1. Giúp người mới vào project hiểu luồng end-to-end
2. Biết file nào đang đảm nhận phần nào
3. Biết dữ liệu đi từ giao diện xuống database ra sao
4. Có thể dùng để demo hoặc bàn giao

---

## 2. Bức tranh tổng thể

Đây là project PHP thuần theo mô hình MVC đơn giản:

1. `Router` nhận request
2. `Controller` quyết định xử lý
3. `Model` làm việc với MySQL
4. `View` render HTML
5. `JavaScript` xử lý các phần tương tác như thêm giỏ, cập nhật giỏ và chuyển sang checkout

Điểm quan trọng:

1. Đây không phải SPA
2. Phần lớn giao diện là server-rendered
3. Các thao tác nhỏ như thêm giỏ, cập nhật giỏ dùng AJAX
4. Bước hoàn tất thanh toán vẫn dùng `POST` truyền thống

---

## 3. File chính trong luồng

### Core

- `backend/public/index.php`
- `backend/routes/web.php`
- `backend/core/Router.php`
- `backend/core/Controller.php`
- `backend/core/Model.php`

### Controller

- `backend/app/controllers/ProductController.php`
- `backend/app/controllers/CartController.php`

### Model

- `backend/app/models/ProductModel.php`
- `backend/app/models/CartModel.php`
- `backend/app/models/OrderModel.php`

### View

- `backend/app/views/users/pages/san-pham.php`
- `backend/app/views/users/pages/cart.php`
- `backend/app/views/users/pages/checkout.php`
- `backend/app/views/users/layouts/main.php`

### JavaScript

- `backend/public/assets/js/user/san-pham.js`
- `backend/public/assets/js/user/cart.js`

### Tài liệu

- `backend/docs/cart-ajax.md`
- `backend/docs/san-pham-cart-checkout.md`

---

## 4. Route của luồng mua hàng

### Trang hiển thị

- `GET /san-pham`
- `GET /cart`
- `GET /gio-hang`
- `GET /checkout`
- `GET /thanh-toan`

### AJAX giỏ hàng

- `POST /cart/add`
- `POST /gio-hang/them`
- `POST /cart/update`
- `POST /gio-hang/cap-nhat`
- `POST /cart/remove`
- `POST /gio-hang/xoa`

### Hoàn tất thanh toán

- `POST /checkout`
- `POST /thanh-toan`

---

## 5. Luồng 1: xem và chọn sản phẩm

### Controller

`ProductController::menu()`

Việc đang làm:

1. Lấy danh sách sản phẩm public từ `ProductModel`
2. Render view `users/pages/san-pham`

### View

`san-pham.php`

Việc đang làm:

1. Hiển thị các nhóm sản phẩm theo danh mục
2. Hiển thị banner, best seller, collection
3. Gắn `data-* attributes` cho JavaScript
4. Gắn URL thêm giỏ và login vào popup sản phẩm

### JavaScript

`san-pham.js`

Việc đang làm:

1. Mở popup chi tiết sản phẩm
2. Tăng giảm số lượng trong popup
3. Gửi AJAX thêm vào giỏ
4. Hiển thị phản hồi thành công hoặc lỗi
5. Hỗ trợ thêm giỏ nhanh từ collection
6. Tìm kiếm sản phẩm theo từ khóa

---

## 6. Luồng 2: thêm sản phẩm vào giỏ

### Dữ liệu gửi lên

Frontend gửi:

```text
POST /cart/add
product_id=...
quantity=...
```

### `CartController::add()`

Controller sẽ kiểm tra:

1. Người dùng đã đăng nhập chưa
2. Role có phải `customer` không
3. `product_id` có hợp lệ không
4. Sản phẩm có tồn tại không
5. Sản phẩm có đang ở trạng thái `active` không

### `CartModel::addItem()`

Model xử lý:

1. Tìm giỏ hàng của khách
2. Nếu chưa có thì tạo mới
3. Thêm sản phẩm vào bảng `cart_items`
4. Nếu sản phẩm đã có sẵn trong giỏ thì cộng dồn số lượng

### Kết quả

Backend trả JSON để frontend:

1. Hiển thị thông báo thành công
2. Báo lỗi nếu không thể thêm giỏ
3. Redirect sang login nếu người dùng chưa đăng nhập

---

## 7. Luồng 3: quản lý giỏ hàng

### Trang giỏ hàng

`cart.php`

Hiện đang có:

1. Danh sách món trong giỏ
2. Checkbox chọn từng món
3. Checkbox chọn tất cả
4. Input số lượng
5. Nút xóa
6. Khối tóm tắt đơn hàng
7. Nút `THANH TOÁN`

### `CartController::index()`

Controller:

1. Kiểm tra người dùng đã đăng nhập
2. Lấy danh sách món trong giỏ
3. Tính tổng tiền giỏ
4. Render trang `cart`

### `cart.js`

JavaScript xử lý:

1. Cập nhật tổng tiền khi tick hoặc bỏ tick món
2. Cập nhật tổng tiền khi sửa số lượng
3. Gọi AJAX cập nhật số lượng
4. Gọi AJAX xóa món
5. Chọn các món để chuyển sang checkout

---

## 8. Luồng 4: mở trang checkout

Khi người dùng bấm `THANH TOÁN`:

1. `cart.js` lấy danh sách `product_id` đang được chọn
2. Chuyển hướng sang:

```text
/checkout?items=1,2,3
```

### `CartController::checkout()`

Controller:

1. Kiểm tra đăng nhập
2. Đọc danh sách `items` từ query string
3. Parse thành mảng `product_id`
4. Gọi `OrderModel::getCheckoutItems()`
5. Nếu không có sản phẩm hợp lệ thì quay lại giỏ hàng
6. Nếu hợp lệ thì render trang `checkout`

---

## 9. Luồng 5: xác nhận thanh toán

### Trang checkout

`checkout.php`

Hiện đang hiển thị:

1. Thông tin giao hàng:
   - địa chỉ
   - họ và tên
   - số điện thoại

2. Phương thức thanh toán:
   - COD
   - Chuyển khoản

3. Tóm tắt đơn hàng:
   - danh sách món
   - số lượng
   - tạm tính
   - tổng đơn hàng

### Submit form

Form gửi:

```text
POST /checkout
```

Kèm theo:

- `selected_items`
- `address`
- `full_name`
- `phone`
- `payment_method`

---

## 10. Luồng 6: tạo đơn hàng

### `CartController::placeOrder()`

Controller xử lý:

1. Kiểm tra đăng nhập
2. Lấy lại danh sách sản phẩm được chọn
3. Lấy lại dữ liệu checkout từ `OrderModel`
4. Kiểm tra dữ liệu form:
   - địa chỉ
   - họ tên
   - số điện thoại
   - phương thức thanh toán

5. Tách `full_name` thành:
   - `first_name`
   - `last_name`

6. Gọi `OrderModel::createOrderFromCartItems()`

7. Nếu tạo đơn thành công:
   - cập nhật lại session thông tin người dùng
   - set flash thành công
   - redirect về `/cart`

### `OrderModel::createOrderFromCartItems()`

Model hiện đang:

1. Tìm `cart_id`
2. Lấy đúng danh sách sản phẩm được chọn
3. Tính tổng tiền
4. Bắt đầu transaction
5. Insert vào bảng `orders`
6. Insert từng dòng vào bảng `order_items`
7. Xóa đúng các món đã checkout khỏi `cart_items`
8. Commit transaction
9. Nếu có lỗi thì rollback

---

## 11. Dữ liệu chính trong database

### Bảng `orders`

Lưu:

1. `Customer_ID`
2. `Customer_phone`
3. `first_name`
4. `last_name`
5. `Shipping_address`
6. `payment_method`
7. `status`
8. `payment_status`
9. `total_price`
10. `created_at`

### Bảng `order_items`

Lưu:

1. `Order_ID`
2. `Product_ID`
3. `quantity`
4. `price_at_purchase`

`price_at_purchase` rất quan trọng vì:

1. Giá sản phẩm có thể thay đổi về sau
2. Nhưng đơn hàng cũ phải giữ đúng giá lúc mua

---

## 12. Trạng thái hiện có

### Trạng thái đơn hàng

- `pending`
- `confirmed`
- `shipping`
- `completed`
- `cancelled`

### Trạng thái thanh toán

- `unpaid`
- `paid`
- `refunded`

### Phương thức thanh toán

- `COD`
- `Bank_Transfer`

---

## 13. Trải nghiệm người dùng hiện tại

Phần giao diện trong luồng này đã được rà lại để hiển thị tiếng Việt có dấu ở các khu vực chính:

1. Thông báo flash
2. Thông báo lỗi controller
3. Nội dung trang giỏ hàng
4. Nội dung trang checkout
5. Thông báo JavaScript trong `cart.js`
6. Thông báo thêm giỏ trong `san-pham.js`

---

## 14. Những điểm cần lưu ý khi phát triển tiếp

1. Nếu muốn hiển thị khuyến mãi thật, nên có bảng giảm giá riêng.
2. Nếu muốn lưu phương thức thanh toán điện tử cụ thể như Momo, VNPay, Stripe thì nên tách rõ enum hoặc bảng cấu hình.
3. Nếu muốn có lịch sử đơn hàng cho người dùng, có thể làm thêm trang `đơn hàng của tôi`.
4. Nếu muốn hỗ trợ kiểm tra tồn kho lúc checkout, cần bổ sung logic `stock_quantity`.
5. Nếu muốn tài liệu đầy đủ hơn nữa, có thể tách riêng thành:
   - tài liệu `sản phẩm`
   - tài liệu `giỏ hàng`
   - tài liệu `checkout`
   - tài liệu `đơn hàng`

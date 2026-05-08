# Tài liệu tính năng quản lý đơn hàng admin

## 1. Mục tiêu

Tài liệu này tóm tắt phần quản lý đơn hàng trong khu vực admin đã được triển khai trong project hiện tại.

Mục tiêu:

1. Giúp nhìn nhanh các chức năng đã làm
2. Biết các file nào đang tham gia xử lý
3. Hiểu route nào dùng cho danh sách, chi tiết và cập nhật đơn hàng
4. Dễ dùng để demo, bàn giao hoặc tiếp tục phát triển

---

## 2. Chức năng đã hoàn thành

### 2.1. Trang danh sách đơn hàng

Trang: `GET /admin/orders`

Đã có:

1. Thẻ thống kê nhanh:
   - Tổng đơn hàng
   - Đơn chờ xác nhận
   - Đơn đang giao
   - Doanh thu từ đơn hoàn thành

2. Bảng danh sách đơn hàng:
   - Mã đơn
   - Khách hàng
   - Số món
   - Tổng tiền
   - Thanh toán
   - Trạng thái
   - Ngày đặt
   - Nút xem chi tiết

3. Bộ lọc đơn hàng nằm trên một hàng ngang:
   - Tìm theo mã đơn, tên khách, số điện thoại, email
   - Lọc theo trạng thái đơn
   - Lọc theo trạng thái thanh toán
   - Lọc theo phương thức thanh toán
   - Lọc theo khoảng ngày đặt

4. Nút `Xóa lọc` để quay về danh sách đầy đủ

### 2.2. Trang chi tiết đơn hàng

Trang: `GET /admin/orders/viewdetail?id=...`

Đã có:

1. Hiển thị thông tin tổng quan của đơn:
   - Mã đơn
   - Trạng thái đơn
   - Trạng thái thanh toán
   - Phương thức thanh toán
   - Tổng tiền
   - Ngày đặt

2. Hiển thị thông tin giao hàng:
   - Tên khách hàng
   - Email
   - Số điện thoại
   - Địa chỉ giao hàng

3. Hiển thị danh sách sản phẩm trong đơn:
   - Tên sản phẩm
   - Mã sản phẩm
   - Số lượng
   - Đơn giá tại thời điểm mua
   - Tạm tính

4. Có form cập nhật đơn hàng ngay trên trang chi tiết:
   - Cập nhật trạng thái đơn hàng
   - Cập nhật trạng thái thanh toán

5. Có nút:
   - `Lưu thay đổi`
   - `In đơn hàng`
   - `Quay lại danh sách`

### 2.3. Cập nhật trạng thái đơn hàng

Route: `POST /admin/orders/update?id=...`

Đã xử lý:

1. Validate `id` đơn hàng
2. Validate trạng thái đơn hợp lệ:
   - `pending`
   - `confirmed`
   - `shipping`
   - `completed`
   - `cancelled`

3. Validate trạng thái thanh toán hợp lệ:
   - `unpaid`
   - `paid`
   - `refunded`

4. Cập nhật lại dữ liệu trong bảng `orders`
5. Hiển thị flash message thành công hoặc thất bại
6. Sau khi lưu sẽ quay lại đúng trang chi tiết đơn hàng

---

## 3. Giao diện hiện tại

### 3.1. Danh sách đơn hàng

File view:

- `backend/app/views/admin/pages/orders.php`

Đặc điểm giao diện:

1. Dùng layout admin có sẵn
2. Dùng card thống kê ở đầu trang
3. Dùng bảng DataTable trong admin theme
4. Bộ lọc được chỉnh thành một hàng ngang duy nhất
5. Toàn bộ nhãn chính đã được đổi sang tiếng Việt có dấu

### 3.2. Chi tiết đơn hàng

File view:

- `backend/app/views/admin/pages/order-detail.php`

Đặc điểm giao diện:

1. Là trang riêng, không còn dùng modal
2. Chia bố cục thành:
   - Cột trái: thông tin đơn hàng + thông tin giao hàng
   - Cột phải: form cập nhật + bảng sản phẩm
3. Có thể in nhanh bằng `window.print()`

---

## 4. Kiến trúc xử lý

### 4.1. Controller

File:

- `backend/app/controllers/OrderController.php`

Các method hiện có:

1. `index()`
   - Render danh sách đơn hàng
   - Nhận bộ lọc từ query string
   - Gọi model lấy dữ liệu danh sách và thống kê

2. `viewDetail()`
   - Render trang chi tiết đơn hàng
   - Lấy thông tin đơn theo `id`
   - Lấy danh sách sản phẩm trong đơn

3. `update()`
   - Nhận submit cập nhật trạng thái
   - Validate dữ liệu
   - Gọi model cập nhật DB
   - Redirect lại trang chi tiết

### 4.2. Model

File:

- `backend/app/models/OrderModel.php`

Các method admin đã thêm:

1. `getAdminOrders(array $filters = [])`
   - Lấy danh sách đơn hàng cho admin
   - Có hỗ trợ lọc theo từ khóa, trạng thái, thanh toán, ngày

2. `getAdminOrderStats()`
   - Lấy số liệu tổng quan để hiển thị card thống kê

3. `findAdminOrderById(int $orderId)`
   - Lấy thông tin một đơn hàng

4. `getOrderItemsByOrderId(int $orderId)`
   - Lấy danh sách sản phẩm trong đơn

5. `updateAdminOrder(int $orderId, string $status, string $paymentStatus)`
   - Cập nhật trạng thái đơn và trạng thái thanh toán

---

## 5. Route hiện tại

Trong file:

- `backend/routes/web.php`

Các route chính:

1. `GET /admin/orders`
   - Mở trang danh sách đơn hàng

2. `GET /admin/orders/viewdetail?id=...`
   - Mở trang chi tiết đơn hàng

3. `POST /admin/orders/update?id=...`
   - Cập nhật trạng thái đơn hàng

---

## 6. File liên quan

### Backend

- `backend/public/index.php`
- `backend/routes/web.php`
- `backend/app/controllers/OrderController.php`
- `backend/app/models/OrderModel.php`

### View

- `backend/app/views/admin/pages/orders.php`
- `backend/app/views/admin/pages/order-detail.php`
- `backend/app/views/admin/layouts/main.php`

---

## 7. Luồng sử dụng

### 7.1. Xem danh sách đơn hàng

1. Admin vào `/admin/orders`
2. Hệ thống hiển thị thống kê và bảng đơn hàng
3. Admin có thể nhập điều kiện lọc trên hàng bộ lọc
4. Hệ thống render lại danh sách phù hợp

### 7.2. Xem chi tiết đơn hàng

1. Admin bấm `Chi tiết` ở một dòng đơn hàng
2. Hệ thống mở trang `/admin/orders/viewdetail?id=...`
3. Trang hiển thị đầy đủ thông tin đơn và sản phẩm trong đơn

### 7.3. Cập nhật đơn hàng

1. Admin đổi trạng thái đơn hàng hoặc trạng thái thanh toán
2. Bấm `Lưu thay đổi`
3. Form gửi `POST /admin/orders/update?id=...`
4. Controller validate dữ liệu
5. Model cập nhật bảng `orders`
6. Hệ thống quay lại trang chi tiết và hiển thị thông báo

---

## 8. Trạng thái dữ liệu đang dùng

### 8.1. Trạng thái đơn hàng

- `pending`: Chờ xác nhận
- `confirmed`: Đã xác nhận
- `shipping`: Đang giao
- `completed`: Hoàn thành
- `cancelled`: Đã hủy

### 8.2. Trạng thái thanh toán

- `unpaid`: Chưa thanh toán
- `paid`: Đã thanh toán
- `refunded`: Đã hoàn tiền

### 8.3. Phương thức thanh toán

- `COD`
- `Bank_Transfer`

---

## 9. Ghi chú phát triển tiếp

Một số hướng có thể làm tiếp:

1. Thêm trang hóa đơn in riêng thay vì chỉ `window.print()`
2. Thêm xuất Excel hoặc CSV
3. Thêm ghi chú nội bộ cho admin
4. Thêm lịch sử thay đổi trạng thái đơn hàng
5. Thêm bộ lọc nâng cao theo khoảng giá trị đơn
6. Sửa toàn bộ layout admin còn lỗi font/encoding sang tiếng Việt có dấu đồng bộ

# UniPhin

Website bán hàng và quản trị nội dung cho UniPhin Coffee, viết bằng PHP thuần theo cấu trúc MVC đơn giản.

## Tổng quan

Project gồm 2 khu vực chính:

- Public/customer: trang chủ, giới thiệu, sản phẩm, giỏ hàng, thanh toán, tin tức, bình luận, tài khoản.
- Admin: quản lý tài khoản, sản phẩm, danh mục sản phẩm, đơn hàng, bài viết, danh mục tin tức, comment, FAQ, profile và nội dung trang.

## Tech stack

- PHP thuần
- MySQL / MariaDB
- MySQLi
- Apache
- HTML / CSS / JavaScript
- XAMPP để chạy local

## Yêu cầu môi trường

- PHP 8.x
- Apache 2.4+
- MySQL hoặc MariaDB
- XAMPP khuyến nghị cho môi trường local

## Cấu hình và chạy local

### 1. Đặt source vào web root

Repo hiện tại được kỳ vọng nằm ở:

```text
c:\xampp\htdocs\uniphin-coffe-v2
```

### 2. Tạo database

Trong source hiện tại, file cấu hình DB là:

- `backend/config/database.php`

Cấu hình mặc định:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'shop_db2');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

Nên tạo database tên `shop_db2` để khỏi phải đổi config.

### 3. Import database

Import file:

```text
database/shop_db2.sql
```

### 4. Bật Apache và MySQL

Khởi động:

- Apache
- MySQL

### 5. Truy cập project

Base URL local:

```text
http://localhost/uniphin-coffe-v2/backend/public
```

## Tài khoản test

Theo dump SQL hiện tại:

- Admin: `admin@gmail.com` / `admin`
- Customer: `customer1@gmail.com` / `customer`

## Cấu trúc thư mục

```text
uniphin-coffe-v2/
|-- README.md
|-- database/
|   `-- shop_db2.sql
`-- backend/
    |-- app/
    |   |-- controllers/
    |   |-- middleware/
    |   |-- models/
    |   `-- views/
    |-- config/
    |-- core/
    |-- public/
    `-- routes/
```

Ý nghĩa nhanh:

- `backend/app/controllers`: xử lý request và nghiệp vụ.
- `backend/app/models`: truy vấn dữ liệu.
- `backend/app/middleware`: phân quyền và kiểm tra đăng nhập.
- `backend/app/views`: giao diện public và admin.
- `backend/config`: cấu hình hệ thống và DB.
- `backend/core`: lớp nền của app.
- `backend/public`: entry point, assets, uploads.
- `backend/routes`: khai báo route.

## Luồng chạy

```text
Request
-> backend/public/index.php
-> backend/routes/web.php
-> Controller
-> Model / View
-> Response
```

## Chức năng hiện có

### Customer side

- Đăng ký, đăng nhập, đăng xuất
- Cập nhật hồ sơ cá nhân
- Xem danh sách sản phẩm
- Thêm, sửa, xóa giỏ hàng
- Chọn item và đặt hàng
- Xem tin tức và chi tiết bài viết
- Comment, reply, edit, delete comment của chính mình
- Xem FAQ, giới thiệu, liên hệ, điều khoản

### Admin side

- Quản lý tài khoản người dùng
- Quản lý sản phẩm
- Quản lý danh mục sản phẩm
- Quản lý đơn hàng
- Quản lý bài viết
- Quản lý danh mục tin tức
- Quản lý comment
- Quản lý FAQ
- Quản lý profile admin
- Chỉnh sửa nội dung trang giới thiệu

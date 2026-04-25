# UNIPHIN Coffee Web App

## Project Overview

**Tên dự án:** UNIPHIN Coffee Web App

**Mô tả ngắn:** Website bán hàng và quản trị nội dung cho UNIPHIN Coffee, gồm khu vực người dùng, đăng nhập/đăng ký, tài khoản cá nhân và dashboard admin.

## Mục tiêu dự án

- Xây dựng website giới thiệu và bán hàng cho người dùng cuối.
- Cung cấp khu vực quản trị để admin quản lý nội dung và dữ liệu hệ thống.
- Tổ chức code theo kiến trúc MVC đơn giản, dễ mở rộng cho team.

## Tech Stack

- **Frontend:** PHP View, HTML, CSS, JavaScript thuần
- **Backend:** PHP thuần, MySQL, MySQLi
- **Web Server:** Apache
- **Môi trường local hiện tại:** XAMPP
- **Khác:** Git

## Getting Started

### Prerequisites

- PHP >= 8.0
- Apache >= 2.4
- MySQL hoặc MariaDB
- XAMPP là lựa chọn phù hợp nhất với cấu trúc hiện tại
- Git

### Clone project

```bash
git clone <repo-url>
cd backend
```

### Cấu hình web root

Project hiện dùng `public/index.php` làm front controller.

Khuyến nghị chạy theo một trong hai cách:

- Trỏ Apache DocumentRoot vào thư mục `public/`
- Hoặc truy cập theo đường dẫn dạng:

```
http://localhost/uniphin2/backend/public
```

### Cấu hình database

Hiện tại project chưa dùng `.env`. Cấu hình đang đặt trực tiếp trong file:

- `config/config.php`
- `config/database.php`

Ví dụ hiện tại:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'shop_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

### Chạy project ở môi trường dev

1. Bật Apache
2. Bật MySQL
3. Import database `shop_db`
4. Truy cập: `http://localhost/uniphin2/backend/public`

## Folder Structure

```
backend/
├─ app/
│  ├─ controllers/
│  │  ├─ AdminController.php
│  │  ├─ AuthController.php
│  │  ├─ PageController.php
│  │  └─ UserController.php
│  ├─ middleware/
│  │  └─ AuthMiddleware.php
│  ├─ models/
│  │  └─ UserModel.php
│  └─ views/
│     ├─ admin/
│     │  ├─ layouts/
│     │  │  └─ main.php
│     │  └─ pages/
│     ├─ auth/
│     │  ├─ signin.php
│     │  └─ signup.php
│     └─ users/
│        ├─ layouts/
│        │  ├─ main.php
│        │  └─ partials/
│        │     ├─ header.php
│        │     ├─ footer.php
│        │     └─ sidebar.php
│        └─ pages/
├─ config/
│  ├─ config.php
│  └─ database.php
├─ core/
│  ├─ Controller.php
│  ├─ Database.php
│  ├─ Model.php
│  └─ Router.php
├─ public/
│  ├─ .htaccess
│  ├─ index.php
│  └─ assets/
│     ├─ admin/
│     ├─ css/
│     ├─ image/
│     └─ js/
└─ routes/
   └─ web.php
```

### Ý nghĩa từng thư mục

- `app/controllers` — xử lý request, điều phối model và view
- `app/models` — truy vấn database
- `app/middleware` — kiểm tra đăng nhập, quyền truy cập
- `app/views` — giao diện render ra browser
- `config` — cấu hình hệ thống và database
- `core` — lớp nền của framework nội bộ
- `public` — entry point và static assets
- `routes` — nơi khai báo route

## Luồng chạy chính

```
Request -> public/index.php -> routes/web.php -> Controller -> Model/View -> Response
```

## Feature Development Guide

### User Side

#### Khi thêm một trang tĩnh mới

- Tạo file view trong `app/views/users/pages/`
- Nếu cần style riêng, tạo CSS trong `public/assets/css/user/`
- Khai báo route trong `routes/web.php`
- Nếu chỉ là trang tĩnh, dùng `PageController`

Ví dụ:

```php
$router->get('/chinh-sach-doi-tra', static function () use ($pageController): void {
    $pageController->show('chinh-sach-doi-tra', 'Chính sách đổi trả');
});
```

#### Khi thêm một trang có dữ liệu động

- Tạo controller riêng, ví dụ `ProductController`
- Tạo method xử lý trong controller
- Gọi model để lấy dữ liệu
- Truyền dữ liệu vào view qua `$this->view(...)`
- Khai báo route trỏ vào controller đó

Ví dụ:

```php
$router->get('/san-pham', static function () use ($productController): void {
    $productController->index();
});
```

#### Khi gọi API hoặc xử lý state

Vì codebase hiện tại không dùng React/Vue:

- State chủ yếu nằm ở PHP render server-side
- JS chỉ nên dùng cho UI interaction nhỏ
- Tránh nhồi quá nhiều business logic vào file JS

#### Khi hiển thị dữ liệu ra view

- Luôn `htmlspecialchars(...)` trước khi render text
- Với ảnh/file upload, luôn chuẩn hóa URL trước khi dùng
- Không truy cập trực tiếp biến chưa được controller truyền vào

### Admin Side

#### Khi tạo một trang quản lý mới

- Tạo method trong `AdminController`
- Gọi `AuthMiddleware::requireAdmin()`
- Render view tương ứng trong `app/views/admin/pages/`
- Thêm route `/admin/...` trong `routes/web.php`

Ví dụ:

```php
public function coupons(): void
{
    AuthMiddleware::requireAdmin();
    $this->view('admin/pages/coupons', ['title' => 'Quản lý mã giảm giá'], 'admin/layouts/main');
}
```

#### Khi làm CRUD

- GET để render danh sách/trang form
- POST để tạo/cập nhật/xóa
- Validate dữ liệu đầu vào ở controller
- Chỉ gọi model sau khi dữ liệu hợp lệ
- Redirect + flash message sau khi xử lý xong

#### Authorization cần nhớ

- Admin page: dùng `AuthMiddleware::requireAdmin()`
- User page cần đăng nhập: dùng `AuthMiddleware::requireLogin()`

#### Validate đầu vào

Luôn kiểm tra:

- field bắt buộc
- email hợp lệ
- độ dài password
- file upload đúng loại và dung lượng
- record có tồn tại trước khi update/delete

## Best Practices & Gotchas

### Auth

- Session là nguồn xác định đăng nhập hiện tại
- Redirect theo role sau login nên tập trung trong controller auth
- Với password mới, ưu tiên `password_hash()` + `password_verify()`

### Quy ước commit message

- `feat:` thêm tính năng
- `fix:` sửa bug
- `refactor:` cải tổ code không đổi behavior
- `style:` sửa giao diện/CSS
- `docs:` cập nhật tài liệu

## Tóm tắt nhanh cho dev mới

- **Entry point:** `public/index.php`
- **Route:** `routes/web.php`
- **Trang tĩnh:** `PageController`
- **Trang có dữ liệu:** controller riêng
- **DB config:** `config/database.php`
- **Asset public:** `public/assets/`
- **User auth:** `AuthController`
- **User account:** `UserController`
- **Admin authz:** `AuthMiddleware::requireAdmin()`

## tài khoản test

**Admin:** admin@gmail.com - admin123
**User:** customer1@gmail.com - customer123

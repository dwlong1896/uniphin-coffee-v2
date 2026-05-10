# UniPhin2

Website ban hang va quan tri noi dung cho UniPhin Coffee, viet bang PHP thuan theo cau truc MVC don gian.

## Tong quan

Project gom 2 khu vuc chinh:

- Public/customer: trang chu, gioi thieu, san pham, gio hang, thanh toan, tin tuc, binh luan, tai khoan.
- Admin: quan ly tai khoan, san pham, danh muc san pham, don hang, bai viet, danh muc tin tuc, comment, FAQ, profile va noi dung trang.

Luu y theo source hien tai:

- Khong con admin dashboard route.
- Sau khi admin dang nhap, he thong redirect ve `GET /admin/users`.
- Route danh muc san pham da tach rieng thanh `/admin/product-categories/*`.

## Tech stack

- PHP thuan
- MySQL / MariaDB
- MySQLi
- Apache
- HTML / CSS / JavaScript
- XAMPP de chay local

## Yeu cau moi truong

- PHP 8.x
- Apache 2.4+
- MySQL hoac MariaDB
- XAMPP khuyen nghi cho moi truong local

## Cau hinh va chay local

### 1. Dat source vao web root

Repo hien tai duoc ky vong nam o:

```text
c:\xampp\htdocs\uniphin2
```

### 2. Tao database

Trong source hien tai, file cau hinh DB la:

- [backend/config/database.php](/c:/xampp/htdocs/uniphin2/backend/config/database.php:1)

Cau hinh mac dinh:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'shop_db2');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

Ban nen tao database ten `shop_db2` de khoi phai doi config.

### 3. Import database

Import file:

```text
database/shop_db2 (1).sql
```

File nay da bao gom schema can thiet cho source hien tai, gom ca:

- `about_sections`
- `faqs.sort_order`
- `faqs.is_active`
- `comments.parent_comment_id`

### 4. Bat Apache va MySQL

Khoi dong:

- Apache
- MySQL

### 5. Truy cap project

Base URL local:

```text
http://localhost/uniphin2/backend/public
```

## Tai khoan test

Theo dump SQL hien tai:

- Admin: `admin@gmail.com` / `admin123`
- Customer: `customer1@gmail.com` / `customer123`

## Cac route chinh

### Public

- `GET /`
- `GET /gioi-thieu`
- `GET /san-pham`
- `GET /cart`
- `GET /checkout`
- `GET /tin-tuc`
- `GET /tin-tuc/{slug}`
- `GET /faqs`
- `GET /dieu-khoan`
- `GET /terms`
- `GET /tai-khoan`

### Auth

- `GET /login`
- `GET /register`
- `POST /login`
- `POST /register`
- `POST /logout`

### Admin

- `GET /admin/users`
- `GET /admin/products`
- `GET /admin/orders`
- `GET /admin/posts`
- `GET /admin/categories`
- `GET /admin/comments`
- `GET /admin/qa`
- `GET /admin/profile`
- `GET /admin/aboutpage`

Route day du hon nam trong:

- [backend/routes/web.php](/c:/xampp/htdocs/uniphin2/backend/routes/web.php:1)

## Cau truc thu muc

```text
uniphin2/
|-- README.md
|-- TESTING_GUIDE.md
|-- database/
|   `-- shop_db2 (1).sql
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

Y nghia nhanh:

- `backend/app/controllers`: xu ly request va nghiep vu.
- `backend/app/models`: truy van du lieu.
- `backend/app/middleware`: phan quyen va kiem tra dang nhap.
- `backend/app/views`: giao dien public va admin.
- `backend/config`: cau hinh he thong va DB.
- `backend/core`: lop nen cua app.
- `backend/public`: entry point, assets, uploads.
- `backend/routes`: khai bao route.

## Luong chay

```text
Request
-> backend/public/index.php
-> backend/routes/web.php
-> Controller
-> Model / View
-> Response
```

## Chuc nang hien co

### Customer side

- Dang ky, dang nhap, dang xuat
- Cap nhat ho so ca nhan
- Xem danh sach san pham
- Them, sua, xoa gio hang
- Chon item va dat hang
- Xem tin tuc va chi tiet bai viet
- Comment, reply, edit, delete comment cua chinh minh
- Xem FAQ, gioi thieu, lien he, dieu khoan

### Admin side

- Quan ly tai khoan nguoi dung
- Quan ly san pham
- Quan ly danh muc san pham
- Quan ly don hang
- Quan ly bai viet
- Quan ly danh muc tin tuc
- Quan ly comment
- Quan ly FAQ
- Quan ly profile admin
- Chinh sua noi dung trang gioi thieu

## Ghi chu quan trong cho dev

### 1. Admin landing page

Admin dang nhap xong se vao:

```text
/admin/users
```

Khong con su dung:

```text
/admin/dashboard
```

### 2. Route danh muc san pham va danh muc tin tuc da tach rieng

- Danh muc san pham:
  - `/admin/product-categories/create`
  - `/admin/product-categories/update`
  - `/admin/product-categories/delete`
- Danh muc tin tuc:
  - `/admin/categories/create`
  - `/admin/categories/update`
  - `/admin/categories/delete`

### 3. Terms page co 2 route

Ca 2 route deu mo cung mot view:

- `/dieu-khoan`
- `/terms`

### 4. Tai khoan customer

Link tai khoan public dung route:

```text
/tai-khoan
```

Khong dung:

```text
/account
```

## Testing

Huong dan test chi tiet da duoc cap nhat theo source moi nhat tai:

- [TESTING_GUIDE.md](/c:/xampp/htdocs/uniphin2/TESTING_GUIDE.md:1)

Tai lieu nay bao gom:

- test tung route
- test theo tung nhom chuc nang
- test auth va phan quyen
- test DB sau moi nghiep vu quan trong

## Cac file quan trong de bat dau doc source

- [backend/routes/web.php](/c:/xampp/htdocs/uniphin2/backend/routes/web.php:1)
- [backend/public/index.php](/c:/xampp/htdocs/uniphin2/backend/public/index.php:1)
- [backend/core/Router.php](/c:/xampp/htdocs/uniphin2/backend/core/Router.php:1)
- [backend/config/database.php](/c:/xampp/htdocs/uniphin2/backend/config/database.php:1)
- [backend/app/controllers/AuthController.php](/c:/xampp/htdocs/uniphin2/backend/app/controllers/AuthController.php:1)
- [backend/app/controllers/AdminController.php](/c:/xampp/htdocs/uniphin2/backend/app/controllers/AdminController.php:1)
- [backend/app/controllers/ProductController.php](/c:/xampp/htdocs/uniphin2/backend/app/controllers/ProductController.php:1)
- [backend/app/controllers/CartController.php](/c:/xampp/htdocs/uniphin2/backend/app/controllers/CartController.php:1)
- [backend/app/controllers/NewsController.php](/c:/xampp/htdocs/uniphin2/backend/app/controllers/NewsController.php:1)

## Known notes theo source hien tai

- Checkout hien tai tao order va xoa item khoi gio, nhung chua tru `stock_quantity`.
- Add to cart hien tai check `status` san pham, nhung chua check chat che ton kho.
- Admin filter comment co kha nang lech giua `visible` va `presented`.
- Admin chi sua duoc comment do chinh admin tao theo logic model hien tai.

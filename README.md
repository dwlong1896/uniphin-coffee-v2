# Uniphin2

## Cấu trúc hiện tại

```text
uniphin2
├── backend
│   ├── app
│   │   ├── controllers
│   │   │   ├── AuthController.php
│   │   │   └── PageController.php
│   │   └── views
│   │       ├── auth
│   │       │   ├── signin.php
│   │       │   └── signup.php
│   │       ├── layouts
│   │       │   └── user
│   │       │       ├── main.php
│   │       │       └── partials
│   │       │           ├── footer.php
│   │       │           ├── header.php
│   │       │           └── sidebar.php
│   │       └── pages
│   │           └── placeholder.php
│   ├── config
│   │   ├── config.php
│   │   └── database.php
│   ├── core
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Model.php
│   │   └── Router.php
│   ├── public
│   │   ├── .htaccess
│   │   ├── index.php
│   │   └── assets
│   │       ├── css
│   │       │   └── user
│   │       │       ├── footer.css
│   │       │       ├── header.css
│   │       │       ├── main-layout.css
│   │       │       ├── pages.css
│   │       │       ├── sidebar.css
│   │       │       ├── signin.css
│   │       │       └── signup.css
│   │       ├── image
│   │       └── js
│   │           └── user
│   │               ├── auth.js
│   │               └── sidebar.js
│   └── routes
│       └── web.php
└── database
```

## Luồng chạy chính

1. Request đi vào `backend/public/index.php`.
2. `index.php` nạp config, core classes, các controller, sau đó nạp route trong `backend/routes/web.php`.
3. `Router` dispatch theo method + URL path.
4. Route trang thường đi qua `PageController`, route auth đi qua `AuthController`.
5. Trang thường render qua layout user (`header + sidebar + footer`), trang auth (`/login`, `/register`) render độc lập.

## Các route giao diện hiện có

- `/`
- `/gioi-thieu`
- `/tin-tuc`
- `/san-pham`
- `/lien-he`
- `/faqs`
- `/account`
- `/terms`
- `/login`
- `/register`

## Ý nghĩa các phần chính

- `backend/app/views/layouts/user/main.php`: layout tổng cho trang user.
- `backend/app/views/layouts/user/partials/header.php`: header desktop/mobile.
- `backend/app/views/layouts/user/partials/sidebar.php`: menu sidebar responsive.
- `backend/app/views/layouts/user/partials/footer.php`: footer user.
- `backend/public/assets/css/user/sidebar.css`: style sidebar và overlay.
- `backend/public/assets/js/user/sidebar.js`: mở/đóng sidebar.
- `backend/app/views/auth/signin.php`, `backend/app/views/auth/signup.php`: giao diện đăng nhập/đăng ký.
- `backend/public/assets/css/user/signin.css`, `backend/public/assets/css/user/signup.css`: style auth pages.
- `backend/public/assets/js/user/auth.js`: ẩn/hiện mật khẩu.

## Responsive menu hiện tại

- Từ `1097px` trở lên: hiển thị nav desktop, ẩn nút 3 gạch và sidebar.
- Từ `1096px` trở xuống: ẩn nav desktop, hiện nút 3 gạch và dùng sidebar.

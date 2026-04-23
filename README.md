# Uniphin2

## Cấu trúc hiện tại

```text
uniphin2
├── backend
│   ├── app
│   │   ├── controllers
│   │   │   └── PageController.php
│   │   └── views
│   │       ├── layouts
│   │       │   └── user
│   │       └── pages
│   │           └── placeholder.php
│   ├── config
│   ├── core
│   ├── public
│   │   ├── index.php
│   │   └── assets
│   │       ├── css
│   │       └── image
│   └── routes
│       └── web.php
└── database
```

## Luồng chạy hiện tại

1. Request đi vào `backend/public/index.php`.
2. `index.php` nạp config, core classes, `PageController` và route.
3. Router dispatch request đến route tương ứng trong `backend/routes/web.php`.
4. `PageController` render view `backend/app/views/pages/placeholder.php`.
5. Nội dung trang được bọc bởi layout user gồm header và footer.

## Các route giao diện đang có

- `/`
- `/gioi-thieu`
- `/tin-tuc`
- `/san-pham`
- `/lien-he`
- `/faqs`
- `/register`
- `/login`
- `/account`
- `/terms`

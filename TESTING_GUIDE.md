# Huong Dan Test Toan Bo Website UniPhin2

## 1. Muc tieu

Tai lieu nay dung de test website theo source hien tai trong:

- `backend/routes/web.php`
- `backend/app/controllers/*.php`
- `backend/app/models/*.php`
- `backend/app/views/*.php`

Tai lieu da duoc cap nhat theo source moi nhat voi cac diem quan trong:

- Khong con route `GET /admin/dashboard`.
- Admin sau khi login se vao `GET /admin/users`.
- Route danh muc san pham da tach rieng thanh `POST /admin/product-categories/*`.
- Link public da dung `GET /tai-khoan` va `GET /dieu-khoan`.
- Database source hien tai nam trong `database/shop_db2 (1).sql`.

## 2. Chuan bi moi truong test

### 2.1. Base URL

Neu chay bang XAMPP theo cau truc repo hien tai:

```text
http://localhost/uniphin2/backend/public
```

Tat ca route ben duoi deu hieu la di kem base URL nay.

Vi du:

```text
GET http://localhost/uniphin2/backend/public/login
```

### 2.2. Import database

Source hien tai dang khop voi file:

```text
database/shop_db2 (1).sql
```

File nay da bao gom:

- bang `about_sections`
- cot `sort_order`, `is_active` trong `faqs`
- cot `parent_comment_id` trong `comments`

Khuyen nghi:

1. Tao database moi, vi du `shop_db`.
2. Import duy nhat file `database/shop_db2 (1).sql`.
3. Kiem tra khong con loi thieu bang/cot truoc khi test.

### 2.3. Thu muc upload can ghi duoc

Dam bao cac thu muc sau ghi duoc:

- `backend/public/uploads/`
- `backend/public/uploads/news/`

### 2.4. Tai khoan mau

Theo dump SQL hien tai:

- Admin
  - Email: `admin@gmail.com`
  - Password: `admin123`
- Customer
  - Email: `customer1@gmail.com`
  - Password: `customer123`

Nen chuan bi 3 session:

1. Cua so chua login.
2. Cua so login customer.
3. Cua so login admin.

### 2.5. Cong cu nen dung

- Trinh duyet + DevTools.
- Tab `Network` de xem request AJAX.
- phpMyAdmin hoac SQL client de doi chieu DB.

### 2.6. Query kiem tra nhanh

```sql
SELECT * FROM users ORDER BY ID DESC;
SELECT * FROM customer ORDER BY ID DESC;
SELECT * FROM carts ORDER BY ID DESC;
SELECT * FROM cart_items ORDER BY Cart_ID DESC, Product_ID DESC;
SELECT * FROM orders ORDER BY ID DESC;
SELECT * FROM order_items ORDER BY Order_ID DESC, Product_ID DESC;
SELECT * FROM products ORDER BY ID DESC;
SELECT * FROM product_categories ORDER BY ID DESC;
SELECT * FROM news ORDER BY ID DESC;
SELECT * FROM news_categories ORDER BY ID DESC;
SELECT * FROM comments ORDER BY ID DESC;
SELECT * FROM faqs ORDER BY id DESC;
SELECT * FROM about_sections ORDER BY id ASC;
```

## 3. Danh sach route can test

### 3.1. Public va customer

- `GET /`
- `GET /gioi-thieu`
- `GET /tin-tuc`
- `GET /tin-tuc/{slug}`
- `POST /post-comment`
- `POST /comment-action`
- `GET /san-pham`
- `GET /cart`
- `GET /gio-hang`
- `GET /checkout`
- `GET /thanh-toan`
- `GET /lien-he`
- `GET /faqs`
- `GET /dieu-khoan`
- `GET /terms`
- `GET /tai-khoan`
- `POST /tai-khoan`
- `POST /cart/add`
- `POST /cart/update`
- `POST /cart/remove`
- `POST /checkout`
- `POST /thanh-toan`
- `POST /gio-hang/them`
- `POST /gio-hang/cap-nhat`
- `POST /gio-hang/xoa`
- `GET /login`
- `GET /register`
- `POST /login`
- `POST /register`
- `POST /logout`

### 3.2. Admin

- `GET /admin/users`
- `GET /admin/users/viewdetail`
- `POST /admin/users/update`
- `GET /admin/products`
- `GET /admin/products/viewdetail`
- `POST /admin/product-categories/create`
- `POST /admin/product-categories/update`
- `POST /admin/product-categories/delete`
- `POST /admin/products/create`
- `POST /admin/products/update`
- `POST /admin/products/delete`
- `GET /admin/orders`
- `GET /admin/orders/viewdetail`
- `POST /admin/orders/update`
- `GET /admin/posts`
- `POST /admin/posts/create`
- `POST /admin/posts/update`
- `POST /admin/posts/delete`
- `GET /admin/posts/get-json`
- `GET /admin/posts/get-post-detail-full`
- `GET /admin/comments`
- `POST /admin/comments/toggle`
- `POST /admin/comments/delete`
- `POST /admin/comments/post-admin`
- `GET /admin/comments/get-json`
- `POST /admin/comments/update`
- `GET /admin/categories`
- `POST /admin/categories/create`
- `POST /admin/categories/update`
- `POST /admin/categories/delete`
- `GET /admin/categories/get-json`
- `GET /admin/contacts`
- `GET /admin/qa`
- `GET /admin/profile`
- `POST /admin/profile`
- `GET /admin/homepage`
- `GET /admin/faqpage`
- `GET /admin/contactpage`
- `GET /admin/aboutpage`
- `POST /admin/faq/save`
- `POST /admin/faq/delete`
- `POST /admin/about/save`

## 4. Quy uoc ghi ket qua

Mau ghi ket qua cho tung test case:

```text
Route:
Tien dieu kien:
Buoc test:
Input:
Ket qua mong doi:
Ket qua thuc te:
Ket luan: Pass / Fail
Bang chung: screenshot / SQL / response JSON
```

## 5. Test theo nhom chuc nang

## 5.1. Public static pages

### Route: `GET /`

Muc tieu:

- Trang chu render binh thuong.

Buoc test:

1. Mo `/`.
2. Kiem tra header, menu, footer.
3. Bam cac link chinh.

Ky vong:

- Khong loi PHP.
- Khong trang trang.
- Footer link `Account` ve `/tai-khoan`.
- Footer link terms ve `/dieu-khoan`.

### Route: `GET /gioi-thieu`

Buoc test:

1. Mo `/gioi-thieu`.
2. Kiem tra du lieu cac section.

Ky vong:

- Du lieu doc tu bang `about_sections`.
- Neu thieu bang nay, trang co the loi.

### Route: `GET /lien-he`

Buoc test:

1. Mo `/lien-he`.
2. Kiem tra render giao dien.

Ky vong:

- Trang hien thi duoc.
- Hien tai source khong co route submit contact public, nen chi smoke test giao dien.

### Route: `GET /faqs`

Buoc test:

1. Mo `/faqs`.
2. Kiem tra danh sach FAQ.
3. Test tim kiem tren giao dien neu co JS filter.

Ky vong:

- Du lieu doc tu `FaqModel::getActive()`.
- Chi hien record `is_active = 1`.
- Thu tu theo `sort_order ASC`.

### Route: `GET /dieu-khoan`
### Route: `GET /terms`

Buoc test:

1. Mo `/dieu-khoan`.
2. Mo `/terms`.
3. So sanh noi dung.

Ky vong:

- Ca 2 route deu mo cung view.
- Khong 404.

## 5.2. Authentication

### Route: `GET /login`

Buoc test:

1. Truy cap khi chua login.
2. Truy cap khi dang login customer.
3. Truy cap khi dang login admin.

Ky vong:

- Chua login: hien form login.
- Customer: redirect ve `/`.
- Admin: redirect ve `/admin/users`.

### Route: `GET /register`

Buoc test:

1. Truy cap khi chua login.
2. Truy cap khi dang login customer.
3. Truy cap khi dang login admin.

Ky vong:

- Chua login: hien form register.
- Customer: redirect ve `/`.
- Admin: redirect ve `/admin/users`.

### Route: `POST /login`

Payload:

- `email`
- `password`

Test case:

1. Login dung customer.
2. Login dung admin.
3. Bo trong email.
4. Bo trong password.
5. Sai mat khau.
6. Sai email.
7. User co `status != active`.

Ky vong:

- Customer: tao session va redirect `/`.
- Admin: tao session va redirect `/admin/users`.
- Empty field: `/login?error=empty`.
- Sai thong tin: `/login?error=invalid`.
- User khong active: `/login?error=banned`.

Session can co:

- `user_id`
- `email`
- `role`
- `name`
- `first_name`
- `last_name`
- `phone`
- `address`
- `gender`
- `birth_date`

### Route: `POST /register`

Payload:

- `fullName`
- `email`
- `phone`
- `password`

Test case:

1. Dang ky hop le.
2. Thieu field.
3. Email sai format.
4. Password ngan hon 6 ky tu.
5. Email trung.

Ky vong:

- Hop le: redirect `/login?registered=1`.
- Empty: `/register?error=empty`.
- Email sai: `/register?error=email`.
- Password ngan: `/register?error=password`.
- Trung email: `/register?error=exists`.

Kiem tra DB:

```sql
SELECT * FROM users WHERE email = 'email-vua-test';
SELECT * FROM customer WHERE ID = <id-moi>;
SELECT * FROM carts WHERE Customer_ID = <id-moi>;
```

Ky vong DB:

- Tao user role `customer`.
- Trigger tao record trong `customer`.
- Trigger tao gio hang trong `carts`.

### Route: `POST /logout`

Test case:

1. Logout customer.
2. Logout admin.

Ky vong:

- Session bi xoa.
- Redirect ve `/`.

## 5.3. Customer profile

### Route: `GET /tai-khoan`

Luu y:

- Route nay dang dung `AuthMiddleware::requireLogin()`.
- Middleware nay chi cho `customer`.

Test case:

1. Customer truy cap.
2. Chua login truy cap.
3. Admin truy cap truc tiep.

Ky vong:

- Customer: mo duoc trang profile.
- Chua login: `403 Forbidden`.
- Admin: `403 Forbidden`.

### Route: `POST /tai-khoan`

Payload:

- `first_name`
- `last_name`
- `email`
- `phone`
- `address`
- `gender`
- `birth_date`
- `avatar` optional

Test case:

1. Update text khong doi avatar.
2. Upload `jpg`.
3. Upload `png`.
4. Upload `webp`.
5. Upload file sai dinh dang nhu `pdf`.
6. Doi email sang email da ton tai.

Ky vong:

- Hop le: cap nhat DB, flash success, redirect lai `/tai-khoan`.
- File sai dinh dang: flash error.
- Neu email trung, co the va cham unique DB. Neu bi loi SQL hoac trang vo, ghi nhan bug.

Kiem tra DB:

```sql
SELECT first_name, last_name, email, phone, address, gender, birth_date, image
FROM users
WHERE ID = 2;
```

## 5.4. Public products

### Route: `GET /san-pham`

Buoc test:

1. Mo khi chua login.
2. Mo khi dang login customer.
3. Kiem tra danh sach san pham.

Ky vong:

- Trang hien danh sach public products.
- Chi san pham dung theo logic `ProductModel::getPublicProducts()`.

## 5.5. Cart

### Route: `GET /cart`
### Route: `GET /gio-hang`

Test case:

1. Chua login truy cap.
2. Customer truy cap khi gio co hang.
3. Customer truy cap khi gio trong.
4. Admin truy cap.

Ky vong:

- Chua login: flash error va redirect `/login`.
- Customer: vao trang gio hang.
- Admin: `403 Forbidden` vi middleware customer-only.

### Route: `POST /cart/add`
### Route: `POST /gio-hang/them`

Payload:

- `product_id`
- `quantity`

Test case:

1. Add hop le.
2. Add cung san pham lan 2.
3. Chua login goi route.
4. `product_id = 0`.
5. `product_id` khong ton tai.
6. San pham `inactive`.
7. San pham `archived`.
8. San pham `out_of_stock`.

Ky vong:

- Hop le: JSON `success = true`.
- Chua login: HTTP `401`, co `redirect_url`.
- ID khong hop le: HTTP `422`.
- Khong ton tai: HTTP `404`.
- Status khac `active`: HTTP `422`.

Luu y theo source:

- Hien tai khong co check `stock_quantity` khi add gio.
- Neu san pham con status `active` nhung stock = 0 van co the add. Can test va ghi nhan bug neu xay ra.

### Route: `POST /cart/update`
### Route: `POST /gio-hang/cap-nhat`

Payload:

- `product_id`
- `quantity`

Test case:

1. Tang so luong.
2. Giam so luong ve `1`.
3. Gui `quantity = 0`.
4. Gui `product_id` khong co trong gio.
5. Chua login.

Ky vong:

- `quantity = 0` bi ep thanh `1`.
- Hop le: JSON success, co `quantity`, `subtotal`.
- Khong co item trong gio: co the tra `404`.
- Chua login: `401`.

### Route: `POST /cart/remove`
### Route: `POST /gio-hang/xoa`

Payload:

- `product_id`

Test case:

1. Xoa item co trong gio.
2. Xoa item khong ton tai.
3. Chua login.

Ky vong:

- Hop le: JSON success.
- `product_id <= 0`: HTTP `422`.
- Chua login: `401`.

Kiem tra DB sau cart:

```sql
SELECT * FROM cart_items
WHERE Cart_ID = (SELECT ID FROM carts WHERE Customer_ID = 2);
```

## 5.6. Checkout va place order

### Route: `GET /checkout`
### Route: `GET /thanh-toan`

Query:

- `items=11,13`

Test case:

1. Customer mo checkout voi danh sach item hop le.
2. Customer mo checkout voi `items` rong.
3. Customer mo checkout voi item khong co trong gio.
4. Chua login.
5. Admin truy cap.

Ky vong:

- Hop le: render trang checkout voi dung item da chon.
- Khong co item hop le: flash error va redirect `/cart`.
- Chua login: flash error va redirect `/login`.
- Admin: `403 Forbidden`.

### Route: `POST /checkout`
### Route: `POST /thanh-toan`

Payload:

- `address`
- `full_name`
- `phone`
- `payment_method`
- `selected_items`

Test case:

1. Dat hang thanh cong voi `COD`.
2. Dat hang thanh cong voi `Bank_Transfer`.
3. Thieu `address`.
4. Thieu `full_name`.
5. Thieu `phone`.
6. `payment_method` sai gia tri.
7. `selected_items` rong.
8. Chua login.

Ky vong:

- Hop le: tao order, tao order_items, xoa selected items khoi gio, flash success, redirect `/cart`.
- Thieu thong tin: render lai checkout va hien loi.
- Payment method sai: render lai checkout va hien loi.
- Chua login: redirect `/login`.

Kiem tra DB:

```sql
SELECT * FROM orders ORDER BY ID DESC LIMIT 3;
SELECT * FROM order_items ORDER BY Order_ID DESC, Product_ID DESC;
SELECT * FROM cart_items
WHERE Cart_ID = (SELECT ID FROM carts WHERE Customer_ID = 2);
```

Luu y rat quan trong theo source hien tai:

- `OrderModel::createOrderFromCartItems()` co tao `orders` va `order_items`.
- Ham nay co xoa selected items khoi `cart_items`.
- Ham nay KHONG tru `products.stock_quantity`.
- Ham nay KHONG doi `products.status` sang `out_of_stock`.

Vi vay, sau order thanh cong:

- `orders.status` ky vong la `pending`.
- `orders.payment_status` de DB mac dinh `unpaid`.
- `cart_items` bi xoa dung item da checkout.
- `stock_quantity` hien tai van nguyen. Neu nghiep vu muon tru kho thi ghi nhan bug, nhung ve mat source hien tai day la hanh vi dang ton tai.

## 5.7. Public news

### Route: `GET /tin-tuc`

Query co the test:

- `search`
- `category`
- `sort`
- `page`

Test case:

1. Mo trang danh sach.
2. Search co ket qua.
3. Search khong co ket qua.
4. Filter theo category.
5. Sort `newest`.
6. Sort `oldest`.
7. Phan trang.
8. Goi bang AJAX.

Ky vong:

- Render dung danh sach.
- Neu request AJAX, response JSON co:
  - `news`
  - `categories`
  - `totalPages`
  - `currentPage`
  - `filters`

### Route: `GET /tin-tuc/{slug}`

Test case:

1. Mo slug hop le.
2. Mo slug khong ton tai.
3. `comment_sort=newest`.
4. `comment_sort=oldest`.
5. `comment_page=2` neu du data.

Ky vong:

- Slug hop le: hien bai viet, comments, bai lien quan.
- Slug sai: redirect `/tin-tuc`.

## 5.8. Public comments

### Route: `POST /post-comment`

Payload:

- `news_id`
- `content`
- `parent_id` optional

Test case:

1. Customer post comment goc.
2. Customer reply comment.
3. Chua login.
4. `content` rong.
5. `content` > 1000 ky tu.
6. `news_id <= 0`.
7. Goi bang AJAX.
8. Goi bang form thuong.

Ky vong:

- Hop le: insert vao `comments`.
- AJAX thanh cong: JSON `status = success`.
- Form thuong thanh cong: redirect ve trang truoc.
- Chua login:
  - AJAX: JSON error.
  - Form thuong: redirect `/login`.

Kiem tra DB:

```sql
SELECT * FROM comments ORDER BY ID DESC LIMIT 10;
```

Luu y:

- Source dang ghi vao cot `parent_comment_id`.
- Vi vay database can dung dump moi, neu khong cac route comment se loi SQL.

### Route: `POST /comment-action`

Payload:

- `action=edit|delete`
- `comment_id`
- `content` khi edit

Test case:

1. Customer sua comment cua minh.
2. Customer xoa comment cua minh.
3. Customer sua comment cua nguoi khac.
4. Customer xoa comment cua nguoi khac.
5. `comment_id <= 0`.
6. `action` sai.
7. Edit voi content rong.
8. Edit voi content > 1000 ky tu.

Ky vong:

- Chi owner moi sua duoc.
- Chi owner moi xoa duoc.
- Sai quyen: JSON error.
- Sai action: JSON error.

## 5.9. Phan quyen admin

Ap dung cho toan bo route `/admin/*`.

Test toi thieu cho moi route admin:

1. Chua login.
2. Login customer.
3. Login admin.

Ky vong:

- Chua login: `403 Forbidden`.
- Customer: `403 Forbidden`.
- Admin: truy cap binh thuong.

Luu y:

- Admin hien tai vao trang quan tri mac dinh la `/admin/users`.
- Khong con dashboard route de test.

## 5.10. Admin users

### Route: `GET /admin/users`

Query:

- `keyword`
- `role`
- `status`

Test case:

1. Xem danh sach mac dinh.
2. Filter `role=customer`.
3. Filter `role=admin`.
4. Filter `status=active`.
5. Search theo email.
6. Search theo ten.
7. Search theo so dien thoai.

Ky vong:

- Danh sach dung theo filter.

### Route: `GET /admin/users/viewdetail?id={id}`

Test case:

1. Mo user hop le.
2. Mo `id = 0`.
3. Mo `id` khong ton tai.

Ky vong:

- Hop le: vao duoc trang detail.
- Sai id: flash error va redirect ve `/admin/users` kem filter neu co.

### Route: `POST /admin/users/update?id={id}`

Payload:

- `status`
- `new_password`
- `confirm_password`
- `filter_keyword`
- `filter_role`
- `filter_status`

Test case:

1. Doi status `active -> banned`.
2. Doi status `active -> inactive`.
3. Doi status `active -> pending`.
4. Status sai gia tri.
5. Reset password hop le.
6. Password moi < 6 ky tu.
7. Confirm password khong khop.
8. Admin tu khoa chinh minh bang cach doi status sang khac `active`.

Ky vong:

- Status hop le: update thanh cong.
- Password hop le: duoc hash va luu.
- Admin khong the tu doi tai khoan hien tai sang `banned`, `pending`, `inactive`.

## 5.11. Admin products va product categories

### Route: `GET /admin/products`

Buoc test:

1. Mo trang products.
2. Kiem tra bang products.
3. Kiem tra bang categories.

Ky vong:

- Load duoc danh sach san pham.
- Load duoc danh sach `product_categories`.

### Route: `POST /admin/product-categories/create`

Payload:

- `name`

Test case:

1. Tao category hop le.
2. Name rong.
3. Name trung.

Ky vong:

- Thanh cong: flash success, redirect `/admin/products`.
- Loi: flash error.

### Route: `POST /admin/product-categories/update?id={id}`

Test case:

1. Sua hop le.
2. Name rong.
3. Name trung.
4. `id <= 0`.
5. `id` khong ton tai.

Ky vong:

- Hop le: flash success.
- Loi: flash error.

### Route: `POST /admin/product-categories/delete?id={id}`

Test case:

1. Xoa category khong co san pham.
2. Xoa category dang duoc san pham su dung.
3. Xoa `id` khong ton tai.

Ky vong:

- Khong co san pham: xoa duoc.
- Dang duoc dung: bi chan.

### Route: `POST /admin/products/create`

Payload:

- `name`
- `description`
- `status`
- `price`
- `P_Cate_ID`
- `slug`
- `image`

Test case:

1. Tao san pham hop le.
2. Thieu `name`.
3. Thieu `description`.
4. Thieu `slug`.
5. Thieu `price`.
6. Thieu `image`.
7. `status = archive` de xem co duoc map sang `archived` khong.
8. `status` sai gia tri.
9. `price < 0`.
10. `P_Cate_ID` sai.
11. Slug trung.
12. Upload file khong phai anh.
13. Upload anh > 2MB.

Ky vong:

- Hop le: tao record moi, file duoc luu vao `uploads`.
- Neu `status = archive`, source map sang `archived`.
- Cac case invalid: flash error, khong tao san pham.

### Route: `GET /admin/products/viewdetail?id={id}`

Test case:

1. Xem product hop le.
2. `id = 0`.
3. `id` khong ton tai.

Ky vong:

- Hop le: vao trang chi tiet.
- Sai id: `404`.

### Route: `POST /admin/products/update?id={id}`

Test case:

1. Update text hop le.
2. Update voi anh moi hop le.
3. Slug trung.
4. `status = archive`.
5. `status` sai.
6. Category sai.
7. `price < 0`.
8. File anh sai dinh dang.
9. File anh > 2MB.

Ky vong:

- Hop le: cap nhat thanh cong.
- Neu co anh moi: anh cu bi xoa.
- `status = archive` duoc map sang `archived`.

### Route: `POST /admin/products/delete?id={id}`

Test case:

1. Xoa san pham chua co trong order.
2. Xoa san pham da co trong `order_items`.
3. Xoa `id` khong ton tai.

Ky vong:

- Chua co trong order: xoa duoc.
- Da co trong order: bi chan.
- Khi xoa duoc, `cart_items` lien quan cung duoc xoa truoc.

Kiem tra DB:

```sql
SELECT * FROM products ORDER BY ID DESC LIMIT 10;
SELECT * FROM product_categories ORDER BY ID DESC;
```

## 5.12. Admin orders

### Route: `GET /admin/orders`

Query:

- `keyword`
- `status`
- `payment_method`
- `payment_status`
- `start_date`
- `end_date`

Test case:

1. Xem danh sach mac dinh.
2. Loc theo status.
3. Loc theo payment method.
4. Loc theo payment status.
5. Loc theo start/end date.
6. Search theo order ID.
7. Search theo ten khach.
8. Search theo sdt.
9. Search theo email.

Ky vong:

- Danh sach dung filter.
- Box thong ke hien thi duoc.

### Route: `GET /admin/orders/viewdetail?id={id}`

Test case:

1. Xem order hop le.
2. `id = 0`.
3. `id` khong ton tai.

Ky vong:

- Hop le: hien order va order_items.
- Sai id: flash error va redirect `/admin/orders`.

### Route: `POST /admin/orders/update?id={id}`

Payload:

- `status`
- `payment_status`
- `return_query`

Test case:

1. `pending -> confirmed`.
2. `confirmed -> shipping`.
3. `shipping -> completed`.
4. `payment_status unpaid -> paid`.
5. `payment_status paid -> refunded`.
6. `status` sai gia tri.
7. `payment_status` sai gia tri.

Ky vong:

- Gia tri hop le: update thanh cong.
- Gia tri sai: flash error.

Kiem tra trigger loyalty:

```sql
SELECT * FROM orders WHERE ID = <order_id>;
SELECT * FROM customer WHERE ID = <customer_id>;
```

Ky vong:

- Khi update `orders.status` sang `completed`, trigger `update_loyalty_points` tang diem.

## 5.13. Admin news categories

### Route: `GET /admin/categories`

Query:

- `search`
- `sort`
- `page`

Test case:

1. Xem danh sach.
2. Search theo ten.
3. Sort `name_asc`.
4. Sort `name_desc`.
5. Sort `newest`.
6. Sort `oldest`.
7. Page > tong so trang.

Ky vong:

- Danh sach dung.
- Neu page vuot qua tong trang, source redirect ve page cuoi.

### Route: `GET /admin/categories/get-json?id={id}`

Test case:

1. Goi id hop le.
2. Goi id khong ton tai.

Ky vong:

- Hop le: JSON category.
- Khong ton tai: JSON error.

### Route: `POST /admin/categories/create`

Payload:

- `name`

Test case:

1. Name hop le.
2. Name rong.
3. Name 1 ky tu.
4. Name > 50 ky tu.
5. Name co ky tu dac biet.
6. Name trung.

Ky vong:

- Hop le: JSON `status=success`.
- Invalid: JSON `status=error` va co message.

### Route: `POST /admin/categories/update`

Payload:

- `id`
- `name`

Test case:

1. Update hop le.
2. `id <= 0`.
3. Name rong.
4. Name qua ngan.
5. Name qua dai.
6. Name trung category khac.

Ky vong:

- Hop le: JSON `status=success`.
- Invalid: JSON `status=error`.

### Route: `POST /admin/categories/delete`

Payload:

- `id`

Test case:

1. Xoa category khong co post.
2. Xoa category dang co post.
3. Xoa `id <= 0`.
4. Xoa `id` khong ton tai.

Ky vong:

- Khong co post: JSON `success`.
- Dang co post: JSON `error`.

## 5.14. Admin posts

### Route: `GET /admin/posts`

Query:

- `search`
- `category`
- `sort`
- `page`

Test case:

1. Xem danh sach.
2. Search theo title.
3. Filter category.
4. Sort `newest`.
5. Sort `oldest`.
6. Sort `title_asc`.

Ky vong:

- Danh sach dung theo filter.

### Route: `GET /admin/posts/get-json?id={id}`
### Route: `GET /admin/posts/get-post-detail-full?id={id}`

Test case:

1. Goi id hop le.
2. Goi id sai.

Ky vong:

- Tra du lieu JSON cho modal/detail.

### Route: `POST /admin/posts/create`

Payload:

- `title`
- `content`
- `category_id`
- `keywords`
- `meta_description`
- `thumbnail` optional

Test case:

1. Tao bai hop le khong co anh.
2. Tao bai hop le co anh.
3. `title < 10`.
4. `title > 255`.
5. Noi dung rong.
6. `category_id <= 0`.
7. `meta_description > 160`.
8. `keywords > 255`.
9. Anh > 2MB.
10. Anh sai dinh dang.

Ky vong:

- Hop le: JSON `status=success`.
- Slug duoc tao tu dong boi `createUniqueSlug($title)`.
- Invalid: JSON `status=error`.

### Route: `POST /admin/posts/update`

Payload:

- `id`
- `title`
- `content`
- `category_id`
- `status`
- `keywords`
- `meta_description`
- `thumbnail` optional

Test case:

1. Update text hop le.
2. Update `status=archived`.
3. Update voi anh moi.
4. `id <= 0`.
5. Title ngan.
6. Noi dung rong.
7. Category sai.
8. SEO qua dai.

Ky vong:

- Hop le: JSON `status=success`.
- Neu thay anh moi va anh cu khong phai `default-news.png`, anh cu bi xoa.
- Neu `status` sai, source se fallback ve `published` thay vi bao loi. Can test va ghi nhan dung hanh vi hien tai.

### Route: `POST /admin/posts/delete`

Payload:

- `id`

Test case:

1. Xoa bai hop le.
2. Xoa `id <= 0`.
3. Xoa bai khong ton tai.

Ky vong:

- Hop le: JSON success.
- Neu bai co anh thuc va khac `default-news.png`, anh do nen bi xoa.

Luu y:

- Source dang xoa file theo duong dan `$_SERVER['DOCUMENT_ROOT'] . '/public/uploads/news/'`.
- Neu app chay trong subfolder `uniphin2/backend/public`, can test ky xem xoa file co that su dung path dung hay khong.

## 5.15. Admin comments

### Route: `GET /admin/comments`

Co 2 mode:

1. Khong truyen `news_id`.
2. Co `news_id`.

Mode 1 test:

1. Mo `/admin/comments`.
2. Search danh sach bai viet neu co o man hinh list.

Ky vong:

- Hien list bai viet de chon quan ly comment.

Mode 2 route:

```text
GET /admin/comments?news_id=<id>&search=...&status=...&page=...
```

Test case:

1. Xem comments cua bai hop le.
2. `news_id` khong ton tai.
3. Search theo content.
4. Filter status.
5. Phan trang.

Ky vong:

- `news_id` sai: redirect ve `/admin/comments`.
- `news_id` dung: hien danh sach comment cua bai.

Luu y quan trong:

- Controller filter chi nhan `status=visible|hidden`.
- DB va cac ham toggle dang dung `presented|hidden`.
- Day la diem rat de sai. Can test ky va ghi nhan bug neu filter `visible` khong ra du lieu dung.

### Route: `GET /admin/comments/get-json?id={id}`

Test case:

1. Goi id hop le.
2. Goi id sai.

Ky vong:

- Tra du lieu JSON comment.

### Route: `POST /admin/comments/toggle`

Payload:

- `id`

Test case:

1. Toggle `presented -> hidden`.
2. Toggle `hidden -> presented`.
3. `id <= 0`.
4. `id` khong ton tai.

Ky vong:

- Hop le: JSON success.
- Invalid: JSON error.

Kiem tra DB:

```sql
SELECT ID, status FROM comments WHERE ID = <comment_id>;
```

### Route: `POST /admin/comments/delete`

Payload:

- `id`

Test case:

1. Xoa comment hop le.
2. `id <= 0`.
3. `id` khong ton tai.

Ky vong:

- Hop le: JSON success.
- Invalid: JSON error.

### Route: `POST /admin/comments/post-admin`

Payload:

- `news_id`
- `content`
- `parent_id` optional

Test case:

1. Admin comment moi.
2. Admin reply.
3. Noi dung rong.
4. Noi dung > 1000 ky tu.
5. `news_id <= 0`.

Ky vong:

- Hop le: JSON success.
- Invalid: JSON error.

### Route: `POST /admin/comments/update`

Payload:

- `id`
- `content`

Test case:

1. Admin sua comment do chinh admin tao.
2. Admin sua comment cua customer.
3. `id <= 0`.
4. Noi dung rong.
5. Noi dung > 1000 ky tu.

Ky vong theo source hien tai:

- Validation hop le: controller goi `CommentModel::updateComment($commentId, $_SESSION['user_id'], $content)`.
- Model chi update khi `User_ID` cua comment trung voi `$_SESSION['user_id']`.
- Nghia la admin chi sua duoc comment cua chinh admin.
- Neu nghiep vu muon admin sua moi comment thi day la bug/han che can ghi nhan.

## 5.16. Admin FAQ, profile va content pages

### Route: `GET /admin/qa`

Buoc test:

1. Mo trang QA.
2. Kiem tra list FAQ.
3. Kiem tra modal them/sua/xoa.

Ky vong:

- Load duoc `FaqModel::getAllAdmin()`.
- CRUD thuc hien qua `/admin/faq/save` va `/admin/faq/delete`.

### Route: `POST /admin/faq/save`

Payload:

- `id` optional
- `question`
- `answer`
- `sort_order`
- `is_active`

Test case:

1. Tao FAQ moi.
2. Sua FAQ cu.
3. Set `is_active = 0`, sau do refresh `/faqs`.

Ky vong:

- Tao/sua thanh cong.
- FAQ inactive khong hien o public `/faqs`.

### Route: `POST /admin/faq/delete`

Payload:

- `id`

Test case:

1. Xoa FAQ hop le.
2. Xoa id khong ton tai.

Ky vong:

- Xoa hop le: redirect ve `/admin/qa` voi flash success.

### Route: `GET /admin/profile`

Buoc test:

1. Mo profile admin.
2. Test dropdown header `My Profile`.
3. Test button `Log Out`.

Ky vong:

- Dropdown hoat dong binh thuong.
- Bam `My Profile` vao duoc route.
- Bam `Log Out` submit duoc `POST /logout`.

### Route: `POST /admin/profile`

Payload:

- `first_name`
- `last_name`
- `email`
- `phone`
- `address`
- `gender`
- `birth_date`
- `avatar` optional

Test case:

1. Update text.
2. Upload avatar hop le.
3. Upload avatar sai dinh dang.
4. Doi email trung.

Ky vong:

- Hop le: flash success, redirect lai `/admin/profile`.
- File sai: flash error.
- Email trung co the gay loi DB neu khong duoc chan truoc. Neu trang vo hoac loi SQL, ghi nhan bug.

### Route: `GET /admin/aboutpage`

Buoc test:

1. Mo trang About admin.
2. Kiem tra list `about_sections`.

Ky vong:

- Hien du lieu tu bang `about_sections`.

### Route: `POST /admin/about/save`

Payload:

- `id`
- `title`
- `content`
- `image_url`

Test case:

1. Sua section hop le.
2. Refresh `/gioi-thieu` de doi chieu.

Ky vong:

- Public about page thay doi theo DB moi.

### Route: `GET /admin/homepage`
### Route: `GET /admin/faqpage`
### Route: `GET /admin/contactpage`
### Route: `GET /admin/contacts`

Test case:

1. Smoke test tung route.
2. Kiem tra co render duoc layout admin.

Ky vong:

- Trang mo duoc.
- Hien tai source khong co route save tuong ung cho `homepage` va `contactpage`, nen tam thoi chi test hien thi.

## 6. Checklist du lieu sau nghiep vu

### 6.1. Sau register

Kiem tra:

- Co user moi trong `users`.
- Co customer moi trong `customer`.
- Co gio hang moi trong `carts`.

### 6.2. Sau cart add/update/remove

Kiem tra:

- `cart_items` thay doi dung.
- JSON `subtotal` va `quantity` hop le.

### 6.3. Sau place order

Kiem tra:

- Co record moi trong `orders`.
- Co record trong `order_items`.
- Selected items bi xoa khoi `cart_items`.
- `payment_status = unpaid`.
- `status = pending`.
- `stock_quantity` hien tai khong doi theo source.

### 6.4. Sau admin complete order

Kiem tra:

- `orders.status = completed`.
- `customer.loyalty_point` tang dung theo trigger.

### 6.5. Sau CRUD product

Kiem tra:

- DB `products` thay doi dung.
- Slug khong trung.
- File anh duoc tao/xoa dung.

### 6.6. Sau CRUD news/post

Kiem tra:

- DB `news` thay doi dung.
- Slug duoc tao tu dong.
- File anh news duoc tao/xoa dung.

### 6.7. Sau CRUD category

Kiem tra:

- `product_categories` hoac `news_categories` thay doi dung.
- Category dang duoc su dung thi bi chan khi xoa.

## 7. Regression checklist ngan sau cac thay doi route moi

Day la checklist can chay lai moi khi sua route:

1. Login admin phai vao `/admin/users`, khong duoc tro ve dashboard.
2. Logo admin va breadcrumb Home phai tro ve `/admin/users`.
3. Khong con tham chieu route `/admin/dashboard`.
4. Form CRUD product category phai dung `/admin/product-categories/*`.
5. Form CRUD news category phai dung `/admin/categories/*`.
6. Footer link `Account` phai vao `/tai-khoan`.
7. Footer link terms phai vao `/dieu-khoan`.
8. Route `/terms` van phai mo duoc trang dieu khoan.
9. Dropdown admin `My Profile` va `Log Out` phai bam duoc.

## 8. Diem rui ro nen test ky

1. Dat hang hien tai khong tru kho san pham.
2. Add cart hien tai chi check `status`, khong check `stock_quantity`.
3. Filter admin comments dang lech giua `visible` va `presented`.
4. Admin comment update chi sua duoc comment cua chinh admin.
5. Profile update customer/admin khong chan trung email truoc khi update.
6. Xoa anh news trong admin post delete co kha nang sai duong dan tren moi truong XAMPP subfolder.
7. Route public va admin co nhieu form AJAX; can xem ky response JSON thay vi chi nhin UI.

## 9. Thu tu chay test de xuat

1. Import DB va smoke test trang public.
2. Test auth.
3. Test customer profile.
4. Test products + cart.
5. Test checkout + order.
6. Test public news + comments.
7. Test phan quyen admin.
8. Test admin users.
9. Test admin products + product categories.
10. Test admin orders.
11. Test admin news categories + posts + comments.
12. Test admin FAQ + profile + about/content pages.

## 10. Dau ra can co sau 1 vong test day du

Ban nen co:

1. Danh sach route pass/fail.
2. Danh sach bug theo nhom chuc nang.
3. Screenshot hoac video ngan cho bug UI.
4. SQL snapshot truoc va sau cac nghiep vu quan trong.
5. Checklist regression de retest sau moi lan sua.

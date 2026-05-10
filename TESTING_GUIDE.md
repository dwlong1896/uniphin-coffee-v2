# Hướng Dẫn Test Toàn Bộ Website UniPhin2

## 1. Mục tiêu

Tài liệu này dùng để test toàn bộ chức năng của website theo:

1. Từng nhóm nghiệp vụ.
2. Từng route.
3. Từng tình huống `happy path`, validation, phân quyền và dữ liệu sau xử lý.

Phạm vi hiện tại bám theo source code trong:

- `backend/routes/web.php`
- `backend/app/controllers/*.php`
- `backend/app/models/*.php`

## 2. Chuẩn bị môi trường test

### 2.1. URL chạy ứng dụng

Nếu bạn chạy bằng XAMPP đúng cấu trúc repo hiện tại, URL thường là:

```text
http://localhost/uniphin2/backend/public
```

Trong tài liệu bên dưới, mọi route đều hiểu là đi kèm base URL này.

Ví dụ:

```text
GET http://localhost/uniphin2/backend/public/login
```

### 2.2. Import database

Để test đầy đủ, nên import theo đúng thứ tự:

1. `database/shop_db.sql`
2. `database/setup_about_faq.sql`

Lý do:

- `shop_db.sql` tạo schema dữ liệu chính.
- `setup_about_faq.sql` bổ sung bảng `about_sections` và cấu trúc FAQ mới có `sort_order`, `is_active`.

Nếu chỉ import `shop_db.sql` mà không import file bổ sung, các trang/route sau có thể lỗi:

- `/gioi-thieu`
- `/faqs`
- `/admin/qa`
- `/admin/aboutpage`
- `/admin/about/save`
- `/admin/faq/save`
- `/admin/faq/delete`

### 2.3. Thư mục upload cần ghi được

Đảm bảo các thư mục sau có quyền ghi:

- `backend/public/uploads/`
- `backend/public/uploads/news/`

### 2.4. Tài khoản mẫu để test

Theo dữ liệu hiện tại đang dùng trong project:

- Admin:
  - Email: `admin@gmail.com`
  - Password: `admin123`
- Customer:
  - Email: `customer1@gmail.com`
  - Password: `customer123`

Nên chuẩn bị thêm:

1. Một cửa sổ ẩn danh cho khách chưa đăng nhập.
2. Một session customer.
3. Một session admin.

### 2.5. Công cụ nên dùng khi test

Nên dùng đồng thời:

- Trình duyệt để test giao diện.
- `DevTools > Network` để kiểm tra các route AJAX.
- phpMyAdmin hoặc SQL client để đối chiếu dữ liệu DB.

### 2.6. Query kiểm tra nhanh sau test

```sql
SELECT * FROM users ORDER BY ID DESC;
SELECT * FROM carts ORDER BY ID DESC;
SELECT * FROM cart_items ORDER BY Cart_ID DESC, Product_ID DESC;
SELECT * FROM orders ORDER BY ID DESC;
SELECT * FROM order_items ORDER BY Order_ID DESC, Product_ID DESC;
SELECT * FROM products ORDER BY ID DESC;
SELECT * FROM news ORDER BY ID DESC;
SELECT * FROM comments ORDER BY ID DESC;
SELECT * FROM news_categories ORDER BY ID DESC;
SELECT * FROM product_categories ORDER BY ID DESC;
SELECT * FROM faqs ORDER BY id DESC;
SELECT * FROM about_sections ORDER BY id ASC;
```

## 3. Quy ước ghi nhận kết quả

Với mỗi test case, nên ghi:

1. Route.
2. Điều kiện trước test.
3. Dữ liệu nhập.
4. Kết quả mong đợi.
5. Kết quả thực tế.
6. Ảnh chụp màn hình hoặc log nếu lỗi.

Mẫu:

```text
Route:
Điều kiện:
Bước test:
Kỳ vọng:
Thực tế:
Kết luận: Pass / Fail
```

## 4. Danh sách route cần test

### 4.1. Public / Customer

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

### 4.2. Admin

- `GET /admin/dashboard`
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

## 5. Test theo nhóm chức năng

## 5.1. Nhóm trang public tĩnh

### Route: `GET /`

Mục tiêu:

- Trang chủ render bình thường.

Bước test:

1. Mở `/`.
2. Quan sát header, menu, footer.
3. Kiểm tra các link chính có bấm được.

Kỳ vọng:

- Không lỗi PHP, không trắng trang.
- Header hiển thị nút `Đăng ký`, `Đăng nhập` nếu chưa login.
- Nếu đã login customer, hiển thị icon giỏ hàng, tài khoản, logout.

### Route: `GET /gioi-thieu`

Mục tiêu:

- Trang giới thiệu đọc được dữ liệu từ `about_sections`.

Bước test:

1. Mở `/gioi-thieu`.
2. Kiểm tra nội dung các block giới thiệu.

Kỳ vọng:

- Trang render thành công.
- Nội dung lấy từ bảng `about_sections`.
- Nếu lỗi `Table 'about_sections' doesn't exist` thì chưa import `setup_about_faq.sql`.

### Route: `GET /lien-he`

Mục tiêu:

- Smoke test trang liên hệ.

Bước test:

1. Mở `/lien-he`.
2. Kiểm tra giao diện hiển thị đầy đủ.

Kỳ vọng:

- Trang mở được.
- Không có route submit liên hệ ở code hiện tại, nên chỉ cần test hiển thị.

### Route: `GET /faqs`

Mục tiêu:

- Trang FAQ đọc dữ liệu active từ DB.

Bước test:

1. Mở `/faqs`.
2. Kiểm tra danh sách câu hỏi/đáp.

Kỳ vọng:

- Chỉ hiện FAQ có `is_active = 1`.
- Thứ tự theo `sort_order ASC`.

### Route: `GET /dieu-khoan`
### Route: `GET /terms`

Mục tiêu:

- Hai route phải mở cùng một trang điều khoản.

Bước test:

1. Mở `/dieu-khoan`.
2. Mở `/terms`.
3. So sánh nội dung.

Kỳ vọng:

- Cả hai route cùng render view điều khoản.
- Không 404.

## 5.2. Nhóm xác thực

### Route: `GET /login`

Bước test:

1. Truy cập khi chưa đăng nhập.
2. Truy cập khi đã đăng nhập customer.
3. Truy cập khi đã đăng nhập admin.

Kỳ vọng:

- Khi chưa đăng nhập: hiển thị form login.
- Khi đã login customer: redirect về `/`.
- Khi đã login admin: redirect về `/admin/dashboard`.

### Route: `GET /register`

Kỳ vọng:

- Khi chưa đăng nhập: hiển thị form đăng ký.
- Khi đã login: redirect theo role như login.

### Route: `POST /login`

Payload chính:

- `email`
- `password`

Test case:

1. Login đúng bằng customer.
2. Login đúng bằng admin.
3. Bỏ trống email/password.
4. Sai mật khẩu.
5. Sai email.
6. Tài khoản bị `banned` hoặc `inactive`.

Kỳ vọng:

- Đúng customer: tạo session và về `/`.
- Đúng admin: tạo session và về `/admin/dashboard`.
- Trống: redirect `/login?error=empty`.
- Sai: redirect `/login?error=invalid`.
- Bị khóa: redirect `/login?error=banned`.

Kiểm tra thêm:

- Session phải có `user_id`, `role`, `email`, `name`.

### Route: `POST /register`

Payload chính:

- `fullName`
- `email`
- `phone`
- `password`

Test case:

1. Đăng ký hợp lệ.
2. Thiếu trường.
3. Email sai format.
4. Password ngắn hơn 6 ký tự.
5. Email trùng.

Kỳ vọng:

- Hợp lệ: tạo user mới role `customer`, redirect `/login?registered=1`.
- Thiếu field: `/register?error=empty`.
- Email sai: `/register?error=email`.
- Password ngắn: `/register?error=password`.
- Email trùng: `/register?error=exists`.

Kiểm tra DB:

```sql
SELECT * FROM users WHERE email = 'email-vua-test';
SELECT * FROM customer WHERE ID = <id-user-moi>;
SELECT * FROM carts WHERE Customer_ID = <id-user-moi>;
```

Kỳ vọng DB:

- Trigger `after_user_insert` tạo `customer` và `cart`.

### Route: `POST /logout`

Test case:

1. Logout customer.
2. Logout admin.

Kỳ vọng:

- Session bị xóa.
- Redirect về `/`.

## 5.3. Nhóm tài khoản khách hàng

### Route: `GET /tai-khoan`

Quyền:

- Chỉ customer.

Test case:

1. Customer truy cập.
2. Chưa login truy cập.
3. Admin truy cập.

Kỳ vọng:

- Customer: mở trang tài khoản.
- Chưa login hoặc admin: `403 Forbidden` theo middleware hiện tại.

### Route: `POST /tai-khoan`

Payload chính:

- `first_name`
- `last_name`
- `email`
- `phone`
- `address`
- `gender`
- `birth_date`
- `avatar` (optional)

Test case:

1. Cập nhật text không upload avatar.
2. Upload avatar `jpg`.
3. Upload avatar `png`.
4. Upload file sai định dạng như `pdf`.
5. Thử đổi email sang email đã tồn tại.

Kỳ vọng:

- Hợp lệ: cập nhật DB, set flash success, redirect lại `/tai-khoan`.
- File sai định dạng: báo lỗi và không lưu.
- Email trùng: kỳ vọng nghiệp vụ là báo lỗi; nếu rơi ra lỗi SQL hoặc trang hỏng thì ghi nhận bug.

Kiểm tra DB:

```sql
SELECT first_name, last_name, email, phone, address, gender, birth_date, image
FROM users
WHERE email = 'customer1@gmail.com';
```

## 5.4. Nhóm sản phẩm public

### Route: `GET /san-pham`

Mục tiêu:

- Hiển thị danh sách sản phẩm public.

Test case:

1. Mở trang sản phẩm khi chưa login.
2. Mở khi đã login customer.

Kỳ vọng:

- Trang hiển thị danh sách sản phẩm active/public theo dữ liệu model.
- Nút thêm giỏ hàng hoạt động cho customer.
- Khi chưa login, thao tác add-to-cart phải yêu cầu login.

## 5.5. Nhóm giỏ hàng

### Route: `GET /cart`
### Route: `GET /gio-hang`

Quyền:

- Chỉ customer.

Test case:

1. Chưa login truy cập.
2. Customer truy cập khi giỏ có hàng.
3. Customer truy cập khi giỏ trống.

Kỳ vọng:

- Chưa login: redirect về `/login`.
- Có hàng: hiển thị item, quantity, subtotal, total.
- Trống: hiển thị trạng thái giỏ trống.

### Route: `POST /cart/add`
### Route: `POST /gio-hang/them`

Payload:

- `product_id`
- `quantity`

Test case:

1. Add 1 sản phẩm hợp lệ khi đã login customer.
2. Add lại cùng sản phẩm để kiểm tra cộng dồn.
3. Chưa login gọi route.
4. `product_id = 0`.
5. `product_id` không tồn tại.
6. Sản phẩm `inactive`, `archived`, `out_of_stock`.

Kỳ vọng:

- Hợp lệ: JSON `success: true`.
- Chưa login: HTTP `401`, trả `redirect_url`.
- ID lỗi: HTTP `422`.
- Không tồn tại: HTTP `404`.
- Status không active: HTTP `422`.

Kiểm tra DB:

```sql
SELECT * FROM cart_items WHERE Cart_ID = (
  SELECT ID FROM carts WHERE Customer_ID = 2
);
```

Lưu ý cần test kỹ:

- Code hiện tại không kiểm tra `stock_quantity` khi add giỏ.
- Nếu thêm được sản phẩm hết hàng, ghi nhận bug nghiệp vụ.

### Route: `POST /cart/update`
### Route: `POST /gio-hang/cap-nhat`

Payload:

- `product_id`
- `quantity`

Test case:

1. Tăng số lượng.
2. Giảm số lượng về `1`.
3. Gửi `quantity = 0`.
4. Gửi `product_id` không tồn tại trong giỏ.
5. Chưa login.

Kỳ vọng:

- `quantity = 0` sẽ bị ép thành `1`.
- Hợp lệ: JSON success + `quantity` + `subtotal`.
- Chưa login: `401`.

### Route: `POST /cart/remove`
### Route: `POST /gio-hang/xoa`

Payload:

- `product_id`

Test case:

1. Xóa sản phẩm có trong giỏ.
2. Xóa sản phẩm không còn trong giỏ.
3. Chưa login.

Kỳ vọng:

- Hợp lệ: JSON success.
- Chưa login: `401`.

## 5.6. Nhóm checkout và đặt hàng

### Route: `GET /checkout`
### Route: `GET /thanh-toan`

Query:

- `items=11,13`

Test case:

1. Customer mở checkout với `items` hợp lệ.
2. Customer mở checkout với `items` rỗng.
3. Chưa login mở checkout.

Kỳ vọng:

- Hợp lệ: render trang checkout với đúng item đã chọn.
- `items` rỗng hoặc không hợp lệ: redirect về `/cart`.
- Chưa login: redirect `/login`.

### Route: `POST /checkout`
### Route: `POST /thanh-toan`

Payload:

- `address`
- `full_name`
- `phone`
- `payment_method`
- `selected_items`

Test case:

1. Đặt hàng thành công với `COD`.
2. Đặt hàng thành công với `Bank_Transfer`.
3. Thiếu `address`.
4. Thiếu `full_name`.
5. Thiếu `phone`.
6. `payment_method` sai giá trị.
7. `selected_items` rỗng.
8. Chưa login.

Kỳ vọng:

- Hợp lệ: tạo order mới, order_items mới, flash success, redirect `/cart`.
- Thiếu dữ liệu: render lại checkout với lỗi.
- Payment sai: render lại checkout với lỗi.
- Chưa login: redirect `/login`.

Kiểm tra DB sau đơn hàng thành công:

```sql
SELECT * FROM orders ORDER BY ID DESC LIMIT 1;
SELECT * FROM order_items ORDER BY Order_ID DESC, Product_ID DESC;
SELECT * FROM cart_items WHERE Cart_ID = (
  SELECT ID FROM carts WHERE Customer_ID = 2
);
```

Kỳ vọng DB:

- Có order mới `status = pending`.
- `payment_status` mặc định `unpaid`.
- Item vừa checkout bị xóa khỏi giỏ.

Điểm phải test rất kỹ:

1. Sau khi đặt hàng, `stock_quantity` có giảm không.
2. Nếu sản phẩm hết kho sau khi mua, `status` có đổi `out_of_stock` không.

Nếu không giảm kho, ghi nhận bug vì stored procedure trong SQL có xử lý kho nhưng code PHP hiện tại đang tạo đơn trực tiếp qua model.

## 5.7. Nhóm tin tức public

### Route: `GET /tin-tuc`

Query hỗ trợ:

- `search`
- `category`
- `sort`
- `page`

Test case:

1. Mở trang danh sách tin.
2. Search theo từ khóa đúng.
3. Search từ khóa không có kết quả.
4. Filter theo category.
5. Sort `newest`.
6. Sort `oldest`.
7. Chuyển trang.
8. Gọi bằng AJAX.

Kỳ vọng:

- Dữ liệu hiển thị đúng filter.
- AJAX phải trả JSON có `news`, `categories`, `totalPages`, `currentPage`, `filters`.

### Route: `GET /tin-tuc/{slug}`

Test case:

1. Mở slug hợp lệ.
2. Mở slug không tồn tại.
3. Đổi `comment_sort=newest`.
4. Đổi `comment_sort=oldest`.
5. Chuyển `comment_page`.

Kỳ vọng:

- Slug hợp lệ: hiển thị chi tiết bài, bình luận, bài liên quan.
- Slug sai: redirect `/tin-tuc`.

## 5.8. Nhóm bình luận public

### Route: `POST /post-comment`

Payload:

- `news_id`
- `content`
- `parent_id` (optional)

Test case:

1. Customer comment gốc.
2. Customer reply comment.
3. Chưa login comment.
4. `content` rỗng.
5. `content` > 1000 ký tự.
6. `news_id` sai.
7. Gửi dạng AJAX.
8. Gửi dạng form thường.

Kỳ vọng:

- Hợp lệ: comment được insert vào DB.
- AJAX: trả JSON `status=success`.
- Form thường: redirect lại bài viết.
- Chưa login: JSON error hoặc redirect login tùy cách gọi.

Kiểm tra DB:

```sql
SELECT * FROM comments ORDER BY ID DESC LIMIT 5;
```

### Route: `POST /comment-action`

Payload:

- `action=edit|delete`
- `comment_id`
- `content` (khi edit)

Test case:

1. Customer sửa comment của chính mình.
2. Customer xóa comment của chính mình.
3. Customer sửa comment của người khác.
4. Customer xóa comment của người khác.
5. `comment_id` sai.
6. `action` sai giá trị.
7. Edit với content rỗng.
8. Edit với content > 1000 ký tự.

Kỳ vọng:

- Chỉ được sửa/xóa comment của chính mình.
- Sai quyền: trả JSON error.
- Sai `comment_id`: JSON error.

## 5.9. Nhóm phân quyền admin

Áp dụng cho toàn bộ route `/admin/*`.

Test case bắt buộc cho từng nhóm admin:

1. Chưa login truy cập.
2. Customer truy cập.
3. Admin truy cập.

Kỳ vọng:

- Chưa login hoặc customer: `403 Forbidden`.
- Admin: truy cập bình thường.

## 5.10. Nhóm admin dashboard

### Route: `GET /admin/dashboard`

Test case:

1. Admin mở dashboard.

Kỳ vọng:

- Trang mở thành công.
- Layout admin, sidebar, header hiển thị bình thường.

## 5.11. Nhóm quản lý người dùng admin

### Route: `GET /admin/users`

Query hỗ trợ:

- `keyword`
- `role`
- `status`

Test case:

1. Xem danh sách mặc định.
2. Filter theo `role=customer`.
3. Filter theo `status=active`.
4. Search theo email.
5. Search theo tên.

Kỳ vọng:

- Danh sách user đúng theo filter.

### Route: `GET /admin/users/viewdetail?id={id}`

Test case:

1. Mở user hợp lệ.
2. Mở `id` không tồn tại.
3. Mở `id` rỗng.

Kỳ vọng:

- Hợp lệ: vào trang chi tiết user.
- Sai id: redirect về `/admin/users` và có flash error.

### Route: `POST /admin/users/update?id={id}`

Payload chính:

- `status`
- `new_password`
- `confirm_password`
- `filter_keyword`
- `filter_role`
- `filter_status`

Test case:

1. Đổi status `active -> banned`.
2. Đổi status `active -> inactive`.
3. Đổi status với giá trị không hợp lệ.
4. Reset password hợp lệ.
5. Reset password ngắn dưới 6 ký tự.
6. Confirm password không khớp.
7. Admin tự ban chính mình.

Kỳ vọng:

- Status hợp lệ: update thành công.
- Password hợp lệ: hash mới được lưu.
- Admin không thể tự đổi trạng thái tài khoản hiện tại sang khác `active`.

## 5.12. Nhóm quản lý sản phẩm admin

### Route: `GET /admin/products`

Test case:

1. Mở danh sách sản phẩm.
2. Kiểm tra bảng sản phẩm.
3. Kiểm tra bảng danh mục sản phẩm.

Kỳ vọng:

- Hiển thị đầy đủ products và product categories.

### Route: `POST /admin/product-categories/create`

Payload:

- `name`

Test case:

1. Tạo danh mục hợp lệ.
2. Tạo trùng tên.
3. Tạo với tên rỗng.

Kỳ vọng:

- Hợp lệ: flash success.
- Trùng hoặc rỗng: flash error.

### Route: `POST /admin/product-categories/update?id={id}`

Test case:

1. Sửa tên hợp lệ.
2. Sửa thành tên trùng.
3. Sửa với `id` không tồn tại.
4. Sửa với tên rỗng.

Kỳ vọng:

- Hợp lệ: cập nhật thành công.
- Lỗi: flash error.

### Route: `POST /admin/product-categories/delete?id={id}`

Test case:

1. Xóa category chưa có sản phẩm.
2. Xóa category đang có sản phẩm.
3. Xóa `id` không tồn tại.

Kỳ vọng:

- Chưa có sản phẩm: xóa thành công.
- Đang có sản phẩm: bị chặn.

### Route: `POST /admin/products/create`

Payload chính:

- `name`
- `description`
- `status`
- `price`
- `P_Cate_ID`
- `slug`
- `image`

Test case:

1. Tạo sản phẩm hợp lệ.
2. Thiếu `name`.
3. Thiếu `slug`.
4. Thiếu `image`.
5. `price < 0`.
6. `status` sai.
7. `P_Cate_ID` không tồn tại.
8. Slug trùng.
9. Upload file không phải ảnh.
10. Upload ảnh > 2MB.

Kỳ vọng:

- Hợp lệ: tạo record mới + file ảnh lưu vào `uploads`.
- Các case invalid: flash error và không tạo sản phẩm.

Kiểm tra DB:

```sql
SELECT * FROM products ORDER BY ID DESC LIMIT 5;
```

### Route: `GET /admin/products/viewdetail?id={id}`

Test case:

1. Xem sản phẩm hợp lệ.
2. Xem `id` không tồn tại.
3. Xem `id` rỗng.

Kỳ vọng:

- Hợp lệ: mở trang chi tiết.
- Sai id: trả 404.

### Route: `POST /admin/products/update?id={id}`

Test case:

1. Update text hợp lệ.
2. Update kèm ảnh mới hợp lệ.
3. Update với slug trùng.
4. Update với status sai.
5. Update với category sai.
6. Update với ảnh > 2MB.
7. Update với file không phải ảnh.

Kỳ vọng:

- Hợp lệ: sản phẩm được cập nhật, ảnh cũ bị xóa nếu có ảnh mới.

### Route: `POST /admin/products/delete?id={id}`

Test case:

1. Xóa sản phẩm chưa từng có trong đơn hàng.
2. Xóa sản phẩm đã xuất hiện trong `order_items`.
3. Xóa sản phẩm không tồn tại.

Kỳ vọng:

- Chưa có trong đơn: xóa thành công.
- Đã có trong đơn: bị chặn.

## 5.13. Nhóm quản lý đơn hàng admin

### Route: `GET /admin/orders`

Query hỗ trợ:

- `keyword`
- `status`
- `payment_method`
- `payment_status`
- `start_date`
- `end_date`

Test case:

1. Xem danh sách mặc định.
2. Lọc theo status.
3. Lọc theo payment method.
4. Lọc theo payment status.
5. Lọc theo ngày.
6. Search theo ID đơn.
7. Search theo email.
8. Search theo tên khách.

Kỳ vọng:

- Danh sách và các box thống kê hiển thị đúng.

### Route: `GET /admin/orders/viewdetail?id={id}`

Test case:

1. Xem đơn hợp lệ.
2. Xem đơn sai id.

Kỳ vọng:

- Hợp lệ: hiển thị order + order items.
- Sai id: redirect về `/admin/orders` với flash error.

### Route: `POST /admin/orders/update?id={id}`

Payload:

- `status`
- `payment_status`
- `return_query`

Test case:

1. Đổi trạng thái `pending -> confirmed`.
2. Đổi `confirmed -> shipping`.
3. Đổi `shipping -> completed`.
4. Đổi payment `unpaid -> paid`.
5. Gửi status sai.
6. Gửi payment_status sai.

Kỳ vọng:

- Giá trị hợp lệ: update thành công.
- Giá trị sai: flash error.

Kiểm tra trigger loyalty:

1. Chọn đơn của customer.
2. Update status sang `completed`.
3. Kiểm tra `customer.loyalty_point`.

Query:

```sql
SELECT * FROM orders WHERE ID = <order_id>;
SELECT * FROM customer WHERE ID = <customer_id>;
```

Kỳ vọng:

- Điểm thưởng tăng theo `FLOOR(total_price / 100000)`.

## 5.14. Nhóm quản lý danh mục tin tức admin

### Route: `GET /admin/categories`

Query hỗ trợ:

- `search`
- `sort`
- `page`

Test case:

1. Xem danh sách category tin tức.
2. Search theo tên.
3. Sort `name_asc`.
4. Sort `name_desc`.
5. Chuyển trang.

Kỳ vọng:

- Danh sách đúng filter.

### Route: `GET /admin/categories/get-json?id={id}`

Test case:

1. Gọi với id hợp lệ.
2. Gọi với id không tồn tại.

Kỳ vọng:

- Hợp lệ: trả JSON category.
- Sai: JSON error.

### Route: `POST /admin/categories/create`

Payload:

- `name`

Test case:

1. Tạo category hợp lệ.
2. Tên rỗng.
3. Tên < 2 ký tự.
4. Tên > 50 ký tự.
5. Tên có ký tự đặc biệt lạ.
6. Tên trùng.

Kỳ vọng:

- AJAX JSON `status=success` khi hợp lệ.
- Validation fail: JSON `status=error`.

### Route: `POST /admin/categories/update`

Payload:

- `id`
- `name`

Test case giống create, cộng thêm:

1. `id` không hợp lệ.
2. Sửa thành tên trùng category khác.

### Route: `POST /admin/categories/delete`

Payload:

- `id`

Test case:

1. Xóa category không có bài viết.
2. Xóa category có bài viết.
3. Xóa `id` không tồn tại.

Kỳ vọng:

- Có bài viết: bị chặn, trả JSON error.
- Không có bài viết: JSON success.

## 5.15. Nhóm quản lý bài viết admin

### Route: `GET /admin/posts`

Query hỗ trợ:

- `search`
- `category`
- `sort`
- `page`

Test case:

1. Xem danh sách post.
2. Search theo tiêu đề.
3. Filter category.
4. Sort `newest`.
5. Sort `oldest`.

Kỳ vọng:

- Bảng bài viết hiển thị đúng.

### Route: `GET /admin/posts/get-json?id={id}`
### Route: `GET /admin/posts/get-post-detail-full?id={id}`

Test case:

1. Gọi với id hợp lệ.
2. Gọi với id sai.

Kỳ vọng:

- Trả JSON dữ liệu bài viết.

### Route: `POST /admin/posts/create`

Payload chính:

- `title`
- `content`
- `category_id`
- `keywords`
- `meta_description`
- `thumbnail` (optional nhưng nên test cả có và không)

Test case:

1. Tạo bài hợp lệ không có ảnh.
2. Tạo bài hợp lệ có ảnh.
3. Tiêu đề < 10 ký tự.
4. Nội dung rỗng.
5. Category không hợp lệ.
6. `meta_description > 160`.
7. `keywords > 255`.
8. Ảnh > 2MB.
9. Ảnh sai định dạng.

Kỳ vọng:

- Hợp lệ: JSON success, DB có post mới, slug tự sinh.

### Route: `POST /admin/posts/update`

Payload chính:

- `id`
- `title`
- `content`
- `category_id`
- `status`
- `keywords`
- `meta_description`
- `thumbnail` (optional)

Test case:

1. Update text hợp lệ.
2. Update đổi trạng thái sang `archived`.
3. Update với ảnh mới.
4. `id` sai.
5. Tiêu đề ngắn.
6. Nội dung rỗng.
7. Category sai.
8. SEO quá dài.

Kỳ vọng:

- Hợp lệ: JSON success.
- Ảnh cũ bị xóa khi thay ảnh mới.

### Route: `POST /admin/posts/delete`

Payload:

- `id`

Test case:

1. Xóa bài hợp lệ.
2. Xóa id không tồn tại.

Kỳ vọng:

- Bài bị xóa khỏi DB.
- Nếu bài có ảnh thật khác `default-news.png`, ảnh nên bị xóa khỏi thư mục.

## 5.16. Nhóm quản lý bình luận admin

### Route: `GET /admin/comments`

Test 2 mode:

1. Không có `news_id`.
2. Có `news_id`.

Mode 1:

- Hiển thị danh sách bài viết để chọn quản lý comment.

Mode 2 query hỗ trợ:

- `news_id`
- `search`
- `status`
- `page`

Test case mode 2:

1. Xem comment bài cụ thể.
2. Search theo nội dung.
3. Filter theo status.
4. Phân trang.

Kỳ vọng:

- Không `news_id`: list bài viết.
- Có `news_id`: list comment bài đó.

Lưu ý test kỹ:

- Code hiện dùng filter `visible/hidden` trong controller, nhưng DB comment đang lưu `presented/hidden`.
- Nếu lọc comment hiển thị không đúng, ghi nhận bug mapping status.

### Route: `GET /admin/comments/get-json?id={id}`

Test case:

1. Gọi với comment hợp lệ.
2. Gọi với id sai.

### Route: `POST /admin/comments/toggle`

Payload:

- `id`

Test case:

1. Toggle từ `presented -> hidden`.
2. Toggle từ `hidden -> presented`.

Kiểm tra DB:

```sql
SELECT ID, status FROM comments WHERE ID = <comment_id>;
```

### Route: `POST /admin/comments/delete`

Payload:

- `id`

Test case:

1. Xóa comment hợp lệ.
2. Xóa id sai.

### Route: `POST /admin/comments/post-admin`

Payload:

- `news_id`
- `content`
- `parent_id` (optional)

Test case:

1. Admin comment mới.
2. Admin reply vào comment có sẵn.
3. Nội dung rỗng.
4. Nội dung > 1000 ký tự.
5. `news_id` sai.

### Route: `POST /admin/comments/update`

Payload:

- `id`
- `content`

Test case:

1. Admin sửa comment do chính admin tạo.
2. Admin sửa comment của customer.

Kỳ vọng:

- Theo code hiện tại, model chỉ cho update khi `User_ID` trùng user hiện tại.
- Vì vậy admin chỉ sửa được comment do chính admin viết.
- Nếu nghiệp vụ muốn admin sửa mọi comment thì đây là điểm cần ghi nhận.

## 5.17. Nhóm FAQ admin

### Route: `GET /admin/qa`

Test case:

1. Mở trang quản lý FAQ.
2. Kiểm tra danh sách FAQ hiện có.

### Route: `POST /admin/faq/save`

Payload:

- `id` (optional)
- `question`
- `answer`
- `sort_order`
- `is_active`

Test case:

1. Tạo FAQ mới.
2. Sửa FAQ cũ.
3. Set `is_active = 0`, sau đó kiểm tra public `/faqs`.

Kỳ vọng:

- FAQ inactive không hiện ở public.

### Route: `POST /admin/faq/delete`

Payload:

- `id`

Test case:

1. Xóa FAQ hợp lệ.
2. Xóa id không tồn tại.

## 5.18. Nhóm profile admin

### Route: `GET /admin/profile`

Test case:

1. Mở trang profile admin.

### Route: `POST /admin/profile`

Payload:

- `first_name`
- `last_name`
- `email`
- `phone`
- `address`
- `gender`
- `birth_date`
- `avatar` (optional)

Test case:

1. Cập nhật text.
2. Upload avatar đúng định dạng.
3. Upload avatar sai định dạng.
4. Đổi email trùng email khác.

Kỳ vọng:

- Hợp lệ: cập nhật thành công.
- File sai: flash error.
- Email trùng: kỳ vọng nên báo lỗi; nếu rơi lỗi SQL thì ghi nhận bug.

## 5.19. Nhóm trang nội dung admin

### Route: `GET /admin/aboutpage`

Test case:

1. Mở trang about admin.
2. Kiểm tra dữ liệu section đang load từ `about_sections`.

### Route: `POST /admin/about/save`

Payload:

- `id`
- `title`
- `content`
- `image_url`

Test case:

1. Sửa section hợp lệ.
2. Refresh `/gioi-thieu` để đối chiếu dữ liệu public.

Kỳ vọng:

- Public about page đổi theo dữ liệu mới.

### Route: `GET /admin/homepage`
### Route: `GET /admin/faqpage`
### Route: `GET /admin/contactpage`
### Route: `GET /admin/contacts`

Mục tiêu:

- Smoke test các trang admin này mở được.

Kỳ vọng:

- Trang render được.
- Không có route save tương ứng cho `homepage` và `contactpage` trong code hiện tại, nên hiện chỉ test hiển thị.

## 6. Checklist phân quyền tối thiểu

Thực hiện cho mọi route quan trọng:

1. Chưa login.
2. Login customer.
3. Login admin.

Checklist mong đợi:

- Route public: ai cũng vào được.
- Route customer như `/tai-khoan`, `/cart`, `/checkout`: chỉ customer.
- Route admin `/admin/*`: chỉ admin.
- AJAX customer khi chưa login nên trả JSON lỗi hoặc redirect URL rõ ràng.

## 7. Checklist dữ liệu sau các nghiệp vụ quan trọng

### 7.1. Sau đăng ký

Kiểm tra:

- Có record mới trong `users`.
- Có record tương ứng trong `customer`.
- Có cart trong `carts`.

### 7.2. Sau add/update/remove cart

Kiểm tra:

- `cart_items` thay đổi đúng.
- `subtotal` trả về đúng.

### 7.3. Sau đặt hàng

Kiểm tra:

1. `orders` có record mới.
2. `order_items` có dữ liệu đúng.
3. `cart_items` của selected items bị xóa.
4. `payment_status = unpaid`.
5. `status = pending`.
6. `stock_quantity` có giảm không.

### 7.4. Sau admin hoàn tất đơn

Kiểm tra:

- `orders.status = completed`.
- `customer.loyalty_point` tăng đúng.

### 7.5. Sau CRUD bài viết

Kiểm tra:

- `news` insert/update/delete đúng.
- `slug` unique.
- File ảnh được tạo/xóa đúng.

### 7.6. Sau CRUD category

Kiểm tra:

- `product_categories` hoặc `news_categories` đổi đúng.
- Trường hợp category còn liên kết dữ liệu thì bị chặn.

## 8. Điểm rủi ro nên test kỹ

Đây là các điểm rất đáng chú ý khi test thực tế:

1. Đặt hàng từ code PHP hiện tại có thể không trừ kho sản phẩm.
2. Add to cart có thể chưa chặn sản phẩm hết hàng nếu `status` vẫn là `active`.
3. Update email ở profile user/admin có thể đụng lỗi unique DB nếu email trùng.
4. Filter status comment trong admin có khả năng lệch giữa `visible` và `presented`.
5. FAQ/About phụ thuộc `setup_about_faq.sql`.

## 9. Cách chạy test đề xuất

Thứ tự khuyên dùng:

1. Test public pages.
2. Test auth.
3. Test customer account.
4. Test cart + checkout.
5. Test news + public comments.
6. Test admin login và phân quyền.
7. Test admin users.
8. Test admin products + product categories.
9. Test admin orders.
10. Test admin news categories + posts + comments.
11. Test FAQ/About/Profile admin.

## 10. Kết quả cuối cùng cần có sau một vòng test đầy đủ

Bạn nên có:

1. Danh sách route pass/fail.
2. Danh sách bug theo nhóm chức năng.
3. Ảnh hoặc video ngắn cho bug UI.
4. SQL snapshot trước và sau các nghiệp vụ quan trọng.
5. Một file tổng hợp regression để retest sau mỗi lần sửa.

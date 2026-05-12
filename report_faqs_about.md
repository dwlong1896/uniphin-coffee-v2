# Report: FAQs và Giới thiệu

## 1. Tổng quan

- Dự án này có hai trang nội dung quan trọng: `FAQs` và `Giới thiệu`.
- Cả hai được xây dựng bằng PHP thuần, theo mô hình MVC nhẹ.
- Trang người dùng và trang admin được tách riêng: người dùng chỉ xem nội dung, admin mới quản lý và chỉnh sửa nội dung.

---

## 2. Trang FAQs

### Thiết kế & thư viện

- Giao diện user `FAQs` dùng:
  - `Tailwind CSS` qua CDN
  - `jQuery`
  - `Slick Slider`
  - `AOS` cho hiệu ứng
  - `FontAwesome` cho icon
- View trang người dùng: `backend/app/views/users/pages/faqs.php`
- Style riêng: `backend/public/assets/css/user/faqs.css`
- JavaScript tương tác: `backend/public/assets/js/user/faqs.js`

### Cách hoạt động

- Controller `PageController::show('faqs', 'FAQs')` tải dữ liệu FAQ từ `FaqModel::getActive()`.
- Chỉ những mục có `is_active = 1` mới hiển thị.
- Câu hỏi được trình bày dạng accordion.
- Có bộ lọc tìm kiếm realtime trên client.

### Sự khác biệt giữa admin và user

- User:
  - Vào `/faqs` để xem câu hỏi và trả lời.
  - Mở/đóng accordion và tìm kiếm câu hỏi.
- Admin:
  - Vào `/admin/qa` để quản lý toàn bộ FAQ.
  - Có thể thêm, chỉnh sửa, xóa, bật/tắt hiển thị, và sắp xếp thứ tự.

### Quản lý câu hỏi/đáp

- Model `FaqModel`: `backend/app/models/FaqModel.php`
- Các phương thức chính:
  - `getActive()` — lấy FAQ đang hiển thị.
  - `getAllAdmin()` — lấy toàn bộ FAQ cho admin.
  - `create()` — thêm câu hỏi mới.
  - `update()` — sửa câu hỏi.
  - `delete()` — xóa câu hỏi.
- Backend xử lý qua:
  - `POST /admin/faq/save`
  - `POST /admin/faq/delete`
- Controller `AdminController::faqSave()` lưu/cập nhật nội dung, `faqDelete()` xử lý xóa.

---

## 3. Trang Giới thiệu

### Thiết kế & thư viện

- Trang user `Giới thiệu` dùng:
  - `Tailwind CSS`
  - `jQuery`
  - `AOS`
  - `FontAwesome`
- View: `backend/app/views/users/pages/gioi-thieu.php`
- Style riêng: `backend/public/assets/css/user/gioi-thieu.css`

### Cấu trúc nội dung

- Trang About có nhiều section:
  - Header giới thiệu thương hiệu.
  - Hero banner.
  - Nguồn gốc.
  - Sứ mệnh.
  - Chất lượng.
  - Phần feedback/đánh giá.
- Dữ liệu text được lấy từ database qua `AboutSectionModel`.

### Dữ liệu động

- `PageController::show('gioi-thieu', ...)` tải dữ liệu từ `AboutSectionModel::getAll()`.
- Các section được map theo `section_key`.
- Nếu DB thiếu dữ liệu thì view vẫn có fallback text mặc định.

### Sự khác biệt giữa admin và user

- User:
  - Vào `/gioi-thieu` để xem thông tin và hình ảnh.
- Admin:
  - Vào `/admin/aboutpage` để sửa nội dung từng section.
  - Có thể sửa `title`, `content`, `image_url`.
- Người dùng thường không có quyền thay đổi nội dung trực tiếp.

### Quản lý nội dung About

- Model `AboutSectionModel`: `backend/app/models/AboutSectionModel.php`
- Phương thức chính:
  - `getAll()` — lấy tất cả section.
  - `update($id, $data)` — cập nhật tiêu đề, nội dung, URL ảnh.
- Form admin: `backend/app/views/admin/pages/aboutpage.php`
- Backend xử lý qua `POST /admin/about/save` và `AdminController::aboutSave()`.

---

## 4. Tổng kết

- `FAQs` và `Giới thiệu` là hai trang nội dung với chức năng xem cho user và quản trị cho admin.
- `FAQs` có thêm chức năng CRUD đầy đủ, bao gồm trạng thái hiển thị và sắp xếp.
- `Giới thiệu` là trang nội dung động, cho phép admin cập nhật nội dung mỗi section và ảnh.
- Cơ chế thay đổi nội dung thông qua trang admin, đảm bảo user thường chỉ xem nội dung.

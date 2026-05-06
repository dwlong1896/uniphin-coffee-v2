# Tài liệu tính năng `san-pham`, `cart`, `checkout`

## 1. Mục tiêu của tài liệu

Tài liệu này giải thích đầy đủ cách hiện thực 3 tính năng liên tiếp của hệ thống người dùng:

1. Trang sản phẩm `san-pham`
2. Giỏ hàng `cart`
3. Thanh toán `checkout`

Mục tiêu là để một người mới vào dự án có thể:

1. Hiểu kiến trúc tổng thể của luồng mua hàng
2. Biết mỗi file đang làm gì
3. Hiểu dữ liệu đi từ giao diện xuống database như thế nào
4. Có thể dùng tài liệu này để thuyết trình hoặc demo tính năng

Tài liệu bám sát code hiện tại trong project, không mô tả lý thuyết chung chung.

---

## 2. Bức tranh tổng thể

Đây là một dự án PHP thuần theo hướng MVC đơn giản:

1. `Router` nhận request
2. `Controller` quyết định xử lý gì
3. `Model` làm việc với MySQL
4. `View` render HTML
5. `JavaScript` chỉ xử lý các phần cần tương tác động như mở modal, thêm giỏ, cập nhật giỏ bằng AJAX

Điểm quan trọng:

1. Dự án không phải SPA
2. Phần lớn giao diện vẫn là server-rendered HTML
3. Chỉ một số thao tác cần phản hồi ngay mới dùng AJAX
4. Checkout hoàn tất đơn hàng vẫn dùng `POST` truyền thống để dễ kiểm soát

---

## 3. Kiến trúc nền đang dùng

### 3.1. Điểm vào ứng dụng

File: `backend/public/index.php`

Vai trò:

1. `session_start()`
2. Nạp config, database, core classes
3. Nạp model và controller
4. Nạp file route
5. Gọi router để dispatch request hiện tại

Luồng:

```text
Browser request
-> public/index.php
-> routes/web.php
-> Router::dispatch()
-> Controller
-> Model
-> View hoặc JSON response
```

### 3.2. Router

File: `backend/core/Router.php`

Vai trò:

1. Lưu danh sách route `GET` và `POST`
2. Chuẩn hóa path
3. Match URL hiện tại với route đã đăng ký
4. Gọi closure hoặc controller method tương ứng

Điểm đáng chú ý:

1. Router có xử lý trường hợp project nằm trong subfolder, ví dụ `/uniphin2/backend/public`
2. Router hỗ trợ route có tham số dạng `/users/{id}`
3. Với feature hiện tại, các route của `san-pham`, `cart`, `checkout` chủ yếu là route tĩnh

### 3.3. Controller base

File: `backend/core/Controller.php`

Các helper quan trọng:

1. `baseUrl($path)`:
   dùng để tạo URL tương đối đúng với thư mục `public`
2. `view($view, $data, $layout)`:
   render view và chèn vào layout
3. `json($data, $statusCode)`:
   trả JSON cho AJAX
4. `setFlash($key, $message)`:
   lưu thông báo session-flash
5. `redirect($url)`:
   chuyển hướng trang

Ý nghĩa trong 3 feature:

1. `san-pham` chủ yếu dùng `view()`
2. `cart/add`, `cart/update`, `cart/remove` dùng `json()`
3. `checkout` dùng `view()` và `redirect()`

### 3.4. Model base

File: `backend/core/Model.php`

Vai trò:

1. Mọi model con kế thừa `Model`
2. Model base lấy sẵn kết nối `mysqli` từ `Database::getInstance()`

Ý nghĩa:

1. Các model như `ProductModel`, `CartModel`, `OrderModel` không cần tự mở kết nối DB
2. Toàn bộ query đi qua cùng một connection

### 3.5. Layout chung của user

File: `backend/app/views/users/layouts/main.php`

Vai trò:

1. Tạo HTML khung chung
2. Nạp header, sidebar, footer
3. Nạp CSS và JS toàn site
4. Hiển thị flash message

Điểm đáng chú ý:

1. `san-pham.css`, `cart.css`, `checkout.css` được nạp ở layout chung
2. `san-pham.js` và `cart.js` cũng được nạp ở layout chung
3. CSS dùng `filemtime()` để tránh cache cũ
4. JS đang dùng `?v=time()` để ép trình duyệt lấy file mới nhất

---

## 4. Công nghệ áp dụng

### 4.1. Backend

1. PHP thuần
2. `mysqli` với prepared statement
3. Session để lưu đăng nhập và flash message
4. MVC tự xây đơn giản

### 4.2. Frontend

1. HTML render từ PHP
2. CSS custom trong `public/assets/css/user`
3. JavaScript native cho modal, add-to-cart, cart AJAX
4. `fetch()` để gọi AJAX
5. `jQuery` để tích hợp plugin slider
6. `Slick Slider` cho banner, best seller, flash sale
7. `AOS` cho animation khi scroll

### 4.3. Cách dùng công nghệ trong feature này

1. `PHP + View`:
   render toàn bộ dữ liệu ban đầu ra HTML
2. `data-* attributes`:
   chuyền dữ liệu từ PHP sang JS mà không cần gọi API thêm
3. `fetch()`:
   dùng cho thao tác nhỏ cần phản hồi ngay
4. `POST form`:
   dùng cho bước hoàn tất checkout
5. `Transaction` trong `OrderModel`:
   đảm bảo tạo order và xóa item khỏi cart diễn ra đồng bộ

---

## 5. Các file chính cần hiểu

| File | Vai trò |
|---|---|
| `backend/public/index.php` | Điểm vào toàn bộ ứng dụng |
| `backend/routes/web.php` | Khai báo route cho sản phẩm, giỏ hàng, checkout |
| `backend/core/Router.php` | Match URL và gọi controller |
| `backend/core/Controller.php` | Helper `view`, `json`, `redirect`, `setFlash` |
| `backend/core/Model.php` | Base model, giữ kết nối DB |
| `backend/app/controllers/ProductController.php` | Xử lý trang sản phẩm |
| `backend/app/controllers/CartController.php` | Xử lý giỏ hàng và checkout |
| `backend/app/models/ProductModel.php` | Lấy dữ liệu sản phẩm |
| `backend/app/models/CartModel.php` | Làm việc với giỏ hàng |
| `backend/app/models/OrderModel.php` | Tạo đơn hàng và lấy dữ liệu checkout |
| `backend/app/views/users/pages/san-pham.php` | Giao diện trang sản phẩm |
| `backend/app/views/users/pages/cart.php` | Giao diện giỏ hàng |
| `backend/app/views/users/pages/checkout.php` | Giao diện thanh toán |
| `backend/public/assets/js/user/san-pham.js` | Tương tác trang sản phẩm |
| `backend/public/assets/js/user/cart.js` | Tương tác trang giỏ hàng |
| `backend/public/assets/css/user/san-pham.css` | Style trang sản phẩm và modal |
| `backend/public/assets/css/user/cart.css` | Style trang giỏ hàng |
| `backend/public/assets/css/user/checkout.css` | Style trang checkout |

---

## 6. Bản đồ route

| HTTP | Route | Controller | Mục đích |
|---|---|---|---|
| `GET` | `/san-pham` | `ProductController::menu()` | Mở trang sản phẩm |
| `GET` | `/cart` | `CartController::index()` | Xem giỏ hàng |
| `GET` | `/gio-hang` | `CartController::index()` | Alias tiếng Việt của cart |
| `POST` | `/cart/add` | `CartController::add()` | Thêm sản phẩm vào giỏ bằng AJAX |
| `POST` | `/gio-hang/them` | `CartController::add()` | Alias tiếng Việt |
| `POST` | `/cart/update` | `CartController::update()` | Cập nhật số lượng bằng AJAX |
| `POST` | `/gio-hang/cap-nhat` | `CartController::update()` | Alias tiếng Việt |
| `POST` | `/cart/remove` | `CartController::remove()` | Xóa sản phẩm khỏi giỏ bằng AJAX |
| `POST` | `/gio-hang/xoa` | `CartController::remove()` | Alias tiếng Việt |
| `GET` | `/checkout` | `CartController::checkout()` | Mở trang thanh toán |
| `GET` | `/thanh-toan` | `CartController::checkout()` | Alias tiếng Việt |
| `POST` | `/checkout` | `CartController::placeOrder()` | Ghi order vào DB |
| `POST` | `/thanh-toan` | `CartController::placeOrder()` | Alias tiếng Việt |

Nhận xét:

1. Feature dùng song song route tiếng Anh và tiếng Việt
2. `san-pham`, `cart`, `checkout` là HTML page route
3. `cart/add`, `cart/update`, `cart/remove` là AJAX endpoint trả JSON

---

## 7. Feature `san-pham`

## 7.1. Vai trò nghiệp vụ

Trang `san-pham` là nơi:

1. Hiển thị danh sách thức uống đang bán
2. Chia sản phẩm theo danh mục
3. Có các khu trình bày như:
   - menu theo category
   - best seller
   - flash sale
   - collection
4. Cho phép mở popup chi tiết sản phẩm
5. Cho phép thêm sản phẩm vào giỏ bằng AJAX

### 7.2. Backend của trang sản phẩm

#### File: `backend/app/controllers/ProductController.php`

Method dùng cho user:

`menu(): void`

Nó làm 2 việc:

1. Gọi `ProductModel->getPublicProducts()`
2. Render view `users/pages/san-pham`

Ý nghĩa:

1. Controller rất gọn
2. Phần xử lý dữ liệu hiển thị chủ yếu nằm ở view

#### File: `backend/app/models/ProductModel.php`

Các method liên quan:

1. `getPublicProducts()`
2. `findById($id)`

##### `getPublicProducts()`

Query:

1. Lấy từ bảng `products`
2. Join `product_categories`
3. Chỉ lấy `WHERE p.status = "active"`
4. Sort theo tên category và `updated_at`

Ý nghĩa:

1. Chỉ sản phẩm active mới ra ngoài trang user
2. Cart cũng đang tái sử dụng logic “chỉ cho thêm hàng active”

##### `findById($id)`

Mục đích:

1. Lấy chi tiết 1 sản phẩm
2. Join ra `category_name`

Hiện tại method này được `CartController::add()` dùng để:

1. xác nhận sản phẩm có tồn tại
2. kiểm tra `status`

### 7.3. View `san-pham.php`

File: `backend/app/views/users/pages/san-pham.php`

Đây là file trung tâm của toàn bộ feature sản phẩm.

#### 7.3.1. Khởi tạo dữ liệu đầu view

Ngay đầu file, view thực hiện:

1. chuẩn hóa biến `$products`
2. tạo helper `$upload()` để build URL ảnh upload
3. tạo helper `$buildDescription()` để sinh mô tả fallback nếu DB chưa có description

Ý nghĩa:

1. Nếu product có mô tả thật trong DB thì dùng mô tả đó
2. Nếu không có, hệ thống vẫn dựng ra một mô tả đủ đẹp để hiển thị popup

#### 7.3.2. Nhóm sản phẩm theo category

View tự dựng mảng `$grouped_products`.

Mỗi phần tử chứa:

1. `id`
2. `name`
3. `sub`
4. `price`
5. `img`
6. `image`
7. `description`
8. `slug`

Ý nghĩa:

1. View đang “tiền xử lý” dữ liệu để render dễ hơn
2. Giá được format sẵn ở đây theo kiểu `30.000 đ`
3. Dữ liệu này được đưa thẳng vào HTML để JS đọc qua `data-*`

#### 7.3.3. Sidebar danh mục

View render sidebar từ chính `$grouped_products`.

Ý tưởng:

1. Category nào có sản phẩm thì mới hiện
2. Mỗi category trở thành anchor link
3. JS sau đó sẽ tự highlight category nào đang active theo scroll

#### 7.3.4. Product grid chính

Mỗi sản phẩm trong grid được render thành:

```html
<article class="uniphin-product-card"
  data-product-id="..."
  data-name="..."
  data-price="..."
  data-description="..."
  data-image="..."
  data-category="..."
  tabindex="0"
  role="button">
```

Đây là điểm cực kỳ quan trọng của feature.

Lý do:

1. Card vừa là phần hiển thị
2. Vừa là “nguồn dữ liệu” để mở modal
3. Không cần gọi API chi tiết sản phẩm riêng

Nói cách khác:

1. Backend render sẵn data vào HTML
2. Frontend chỉ việc đọc lại `dataset`

#### 7.3.5. Khu `BEST SELLER`

`$bestseller_items = array_slice($products, 0, 5);`

Nghĩa là:

1. Best seller hiện tại là 5 sản phẩm đầu tiên của danh sách active
2. Chưa có business rule “best seller thật” từ database

Mỗi item best seller cũng mang:

1. `data-product-id`
2. `data-name`
3. `data-price`
4. `data-description`
5. `data-image`
6. `data-category`

Nó được click để mở cùng một popup với product grid.

#### 7.3.6. Khu `FLASH SALE`

Flash sale đang là lớp hiển thị UI, không phải dữ liệu khuyến mãi thật trong DB.

Hiện tại:

1. cũng lấy từ `bestseller_items`
2. UI giảm giá 10% bằng phép tính trong view
3. phần trăm giảm được render trực tiếp

Điều quan trọng:

1. Giá hiển thị trong flash sale là giá UI
2. Cart và order hiện vẫn lấy giá gốc từ bảng `products`
3. Điều này có nghĩa là flash sale hiện mới là trình diễn giao diện, chưa phải logic khuyến mãi hoàn chỉnh

#### 7.3.7. Khu `COLLECTION`

Collection sử dụng:

`$new_collection = array_slice($products, 0, 4);`

Ý nghĩa:

1. Collection hiện đang lấy 4 sản phẩm đầu tiên từ danh sách active
2. Chưa có bảng hoặc cờ riêng cho “collection”

Mỗi item trong collection là flip-card:

1. mặt trước là ảnh và tên
2. hover thì lật sang mặt sau
3. mặt sau có tên, giá, mô tả ngắn và nút `Đặt ngay`

Nút `Đặt ngay` mang `data-product-id`:

```html
<button class="btn-flip-add" type="button" data-product-id="...">
  Đặt ngay
</button>
```

Khác biệt quan trọng:

1. grid / best seller / flash sale mở modal trước
2. collection không mở modal
3. collection add trực tiếp vào cart luôn

#### 7.3.8. Modal chi tiết sản phẩm

Cuối file có một modal dùng chung cho toàn bộ:

1. product grid
2. best seller
3. flash sale

Modal có:

1. title
2. category
3. price
4. description
5. image
6. quantity `+ -`
7. nút `THÊM VÀO GIỎ`
8. vùng feedback thành công hoặc lỗi

Modal root còn chứa:

1. `data-cart-add-url`
2. `data-login-url`

Ý nghĩa:

1. JS không hardcode endpoint
2. URL được truyền từ PHP sang JS thông qua HTML

### 7.4. JavaScript `san-pham.js`

File: `backend/public/assets/js/user/san-pham.js`

File này gồm 2 nửa rõ ràng.

#### 7.4.1. Phần jQuery

Mục đích:

1. khởi tạo AOS sau khi page load
2. khởi tạo slider banner
3. khởi tạo slider best seller
4. đồng bộ text best seller active
5. khởi tạo slider flash sale
6. chạy countdown

Đây là phần thiên về trình bày giao diện.

#### 7.4.2. Phần JavaScript native

Mục đích:

1. highlight sidebar theo scroll
2. mở và đóng modal
3. đổi số lượng trong modal
4. thêm vào giỏ bằng AJAX
5. thêm trực tiếp từ collection bằng AJAX
6. tìm kiếm sản phẩm trong product grid

### 7.5. Hàm quan trọng trong `san-pham.js`

#### `updateSidebar()`

Vai trò:

1. xác định section nào đang nằm trong viewport
2. thêm class `menu-active` vào đúng category bên trái

#### `openProductModal(card)`

Vai trò:

1. lấy dữ liệu từ `card.dataset`
2. đổ vào modal
3. reset quantity về `1`
4. reset feedback
5. gắn `data-product-id` vào nút add-to-cart

Đây là kỹ thuật “1 modal dùng cho nhiều item”.

#### `closeProductModal()`

Vai trò:

1. ẩn modal
2. bỏ class khóa scroll nền
3. trả focus về item trước đó để trải nghiệm bàn phím tốt hơn

#### `setModalFeedback(message, type)`

Vai trò:

1. hiển thị dòng báo thành công hoặc lỗi trong popup
2. không cần `alert()` cho luồng popup chính

#### `addToCartRequest(productId, quantity)`

Đây là hàm AJAX cốt lõi của trang sản phẩm.

Nó:

1. gọi `fetch(cartAddUrl, { method: "POST" ... })`
2. gửi `product_id` và `quantity`
3. parse JSON response
4. nếu `401` thì redirect sang login
5. nếu lỗi khác thì throw `Error`
6. nếu thành công thì trả dữ liệu JSON

Điểm mạnh của cách này:

1. chỉ viết logic request một lần
2. modal và collection cùng tái sử dụng

### 7.6. Luồng thêm vào giỏ từ popup

```text
User click product card
-> JS mở modal
-> User chọn số lượng
-> User click "THÊM VÀO GIỎ"
-> san-pham.js gọi POST /cart/add
-> CartController::add()
-> ProductModel::findById()
-> CartModel::addItem()
-> DB ghi vào cart_items
-> JSON success
-> san-pham.js hiện feedback "Đã thêm sản phẩm vào giỏ hàng"
```

### 7.7. Luồng thêm vào giỏ từ `collection`

```text
User hover flip-card
-> thấy nút "Đặt ngay"
-> click nút
-> san-pham.js gọi lại addToCartRequest(productId, 1)
-> CartController::add()
-> CartModel::addItem()
-> DB ghi cart_items
-> nút tạm đổi text "Dang them..." rồi "Da them"
```

Đây là cách làm rất phù hợp với yêu cầu:

1. JS ít
2. tái sử dụng logic
3. không tách thành hệ thống riêng cho collection

### 7.8. Tìm kiếm trên trang sản phẩm

Search hiện tại chỉ lọc:

1. các card trong product grid chính

Nó chưa lọc:

1. best seller
2. flash sale
3. collection

Luồng:

1. user gõ vào input
2. JS duyệt từng `.uniphin-product-card`
3. nếu không match tên thì `display: none`
4. nếu section không còn card match thì ẩn luôn section
5. nếu không có kết quả thì hiện `Khong tim thay san pham`

---

## 8. Feature `cart`

## 8.1. Vai trò nghiệp vụ

Cart là nơi:

1. lưu các món user đã chọn
2. cho phép chỉnh số lượng
3. cho phép xóa món
4. cho phép tick chọn món để thanh toán
5. chuyển danh sách món đã chọn sang trang checkout

### 8.2. Controller `CartController.php`

File: `backend/app/controllers/CartController.php`

Đây là controller trung tâm của cả giỏ hàng lẫn thanh toán.

#### `index(): void`

Vai trò:

1. kiểm tra user phải là customer
2. lấy `cartItems` từ `CartModel`
3. tính `cartTotal`
4. render view `cart.php`

#### `add(): void`

Đây là endpoint AJAX thêm vào giỏ.

Các bước:

1. kiểm tra đăng nhập customer
2. đọc `product_id`, `quantity` từ `POST`
3. validate `product_id > 0`
4. gọi `ProductModel::findById()`
5. kiểm tra `status === active`
6. gọi `CartModel::addItem()`
7. trả JSON thành công hoặc lỗi

Điểm rất quan trọng về nghiệp vụ:

1. không kiểm tra `stock_quantity`
2. lý do là sản phẩm đồ ăn/uống được làm khi khách đặt
3. database vẫn có cột tồn kho nhưng logic hiện tại không dùng đến

#### `update(): void`

Endpoint AJAX cập nhật số lượng trong cart.

Luồng:

1. validate login
2. validate `product_id`
3. ép `quantity >= 1`
4. gọi `CartModel::updateItemQuantity()`
5. gọi `CartModel::findItem()` để lấy quantity và subtotal mới
6. trả JSON cho frontend cập nhật giao diện

#### `remove(): void`

Endpoint AJAX xóa item.

Luồng:

1. validate login
2. đọc `product_id`
3. gọi `CartModel::removeItem()`
4. trả JSON success

#### `checkout(): void`

Vai trò:

1. đọc danh sách product id từ query `?items=1,2,3`
2. gọi `OrderModel::getCheckoutItems()`
3. nếu không còn item hợp lệ thì quay lại cart
4. render `checkout.php`

#### `placeOrder(): void`

Đây là bước ghi order thật vào database.

Luồng:

1. validate login customer
2. lấy `selected_items` từ form hidden
3. lấy lại danh sách item từ `OrderModel::getCheckoutItems()`
4. validate các trường giao hàng
5. validate phương thức thanh toán
6. tách `full_name` thành `first_name` và `last_name`
7. gọi `OrderModel::createOrderFromCartItems()`
8. nếu thành công thì cập nhật lại một phần session profile
9. set flash success
10. redirect về `/cart`

### 8.3. Hai helper quan trọng trong `CartController`

#### `isLoggedInCustomer()`

Kiểm tra:

1. có `$_SESSION['user_id']`
2. `$_SESSION['role'] === 'customer'`

Điều này đảm bảo:

1. chỉ customer mới được add cart / checkout
2. admin không dùng giỏ hàng user

#### `parseSelectedItems($raw)`

Vai trò:

1. đọc chuỗi kiểu `1,2,2,5`
2. tách thành mảng số nguyên
3. loại bỏ giá trị rỗng
4. loại bỏ số `<= 0`
5. loại bỏ trùng lặp

Ý nghĩa:

1. query string và hidden input đều được chuẩn hóa trước khi vào model

### 8.4. Model `CartModel.php`

File: `backend/app/models/CartModel.php`

Đây là model chuyên xử lý bảng `carts` và `cart_items`.

#### `findOrCreateCartIdByCustomerId(int $customerId): ?int`

Đây là method quan trọng nhất của cart model.

Nó:

1. tìm cart hiện có của customer
2. nếu chưa có thì tự tạo mới

Ý nghĩa thiết kế:

1. database đã có trigger tạo cart khi tạo customer
2. nhưng code vẫn tự “phòng thủ” bằng cách tự tạo cart nếu thiếu
3. điều này giúp hệ thống bền hơn khi dữ liệu seed hoặc dữ liệu cũ không đồng nhất

#### `addItem(int $customerId, int $productId, int $quantity): bool`

Cách làm:

1. lấy `cartId`
2. `INSERT INTO cart_items`
3. dùng `ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)`

Điểm mạnh:

1. nếu món chưa có thì insert mới
2. nếu món đã có thì chỉ cộng quantity
3. không cần viết thêm bước `SELECT rồi IF`

#### `getItems(int $customerId): array`

Query join:

1. `cart_items`
2. `products`
3. `product_categories`

Trả về:

1. `Product_ID`
2. `quantity`
3. `name`
4. `image`
5. `price`
6. `status`
7. `category_name`
8. `subtotal`

`subtotal` được tính ngay trong SQL:

```sql
(ci.quantity * p.price) AS subtotal
```

#### `updateItemQuantity(...)`

Vai trò:

1. update đúng dòng cart item theo `Cart_ID` và `Product_ID`
2. chỉ sửa quantity

#### `removeItem(...)`

Vai trò:

1. xóa đúng món khỏi cart

#### `findItem(...)`

Vai trò:

1. sau khi update, lấy lại quantity và subtotal mới
2. frontend dùng JSON này để cập nhật DOM

#### `countItems(...)`

Vai trò:

1. cộng tổng quantity toàn giỏ

Hiện tại method này chưa phải trọng tâm của cart page, nhưng hữu ích nếu sau này muốn hiện badge ở header.

### 8.5. View `cart.php`

File: `backend/app/views/users/pages/cart.php`

#### 8.5.1. Nếu giỏ trống

View hiển thị:

1. tiêu đề “Giỏ hàng đang trống”
2. lời nhắc quay lại trang sản phẩm
3. nút `TIEP TUC MUA HANG`

#### 8.5.2. Nếu giỏ có item

View render layout 2 cột:

1. cột trái là danh sách item
2. cột phải là tóm tắt đơn hàng

#### 8.5.3. `data-*` bridge giữa PHP và JS

Root của cart có:

1. `data-update-url`
2. `data-remove-url`
3. `data-login-url`
4. `data-checkout-url`

Điều này giúp:

1. JS biết gọi URL nào
2. không cần hardcode path bên trong file JS

#### 8.5.4. Mỗi dòng item trong cart

Mỗi item được render dạng:

```html
<article data-cart-item data-product-id="..." data-price="...">
```

Bên trong có:

1. checkbox chọn món
2. ảnh món
3. tên món
4. category
5. input số lượng
6. subtotal dòng
7. nút `Xoa`

Subtotal còn được JS lưu vào `data-value` để tính tổng nhanh hơn.

#### 8.5.5. Phần summary

Summary panel có:

1. `Tam tinh`
2. `Phi van chuyen`
3. `Tong don hang`
4. nút `THANH TOAN`
5. note số món đang có trong giỏ

### 8.6. JavaScript `cart.js`

File: `backend/public/assets/js/user/cart.js`

Đây là file JS tối giản, tập trung đúng 4 việc:

1. chọn hoặc bỏ chọn món
2. cập nhật số lượng bằng AJAX
3. xóa món bằng AJAX
4. chuyển sang checkout với danh sách món đã tick

#### `formatCurrency(value)`

Vai trò:

1. format tiền theo `vi-VN`
2. thêm hậu tố `d`

#### `checkedItems()`

Vai trò:

1. trả về các item đang được tick

#### `recalculate()`

Đây là hàm quan trọng nhất của cart frontend.

Nó:

1. lấy tất cả item đang tick
2. cộng `data-item-subtotal`
3. cập nhật `Tam tinh`
4. cập nhật `Tong don hang`
5. cập nhật trạng thái checkbox `Chon tat ca`
6. cập nhật dòng note số món trong giỏ

Điểm cần hiểu:

1. checkbox chỉ ảnh hưởng đến số tiền được tính để checkout
2. checkbox không xóa dữ liệu trong cart
3. cart item vẫn tồn tại, chỉ là có được chọn để thanh toán hay không

#### `postForm(url, payload)`

Đây là helper AJAX chung của cart page.

Nó:

1. gửi `POST`
2. parse JSON response
3. nếu `401` thì chuyển sang login
4. nếu response lỗi thì throw `Error`

Tương tự `addToCartRequest()` ở `san-pham.js`, đây là cách gom logic request vào một nơi.

#### Luồng cập nhật số lượng

Khi user sửa input số lượng:

1. JS bắt event `change`
2. ép quantity >= 1
3. disable input trong lúc gửi request
4. `POST /cart/update`
5. nhận `quantity` và `subtotal` mới
6. cập nhật lại DOM
7. gọi `recalculate()`

Nếu lỗi:

1. JS trả input về giá trị cũ
2. hiện `alert()`

#### Luồng xóa item

Khi user click `Xoa`:

1. JS bắt click
2. lấy `product_id`
3. `POST /cart/remove`
4. nếu thành công thì xóa node item khỏi DOM
5. gọi `recalculate()`
6. nếu giỏ trống thì `window.location.reload()` để hiện empty state server-render

#### Luồng checkout

Khi user click `THANH TOAN`:

1. JS lấy tất cả item đang tick
2. tạo chuỗi `1,3,5`
3. redirect sang `/checkout?items=1,3,5`

Điểm rất quan trọng:

1. cart page không tự tạo order
2. cart page chỉ chọn món và gửi danh sách sang checkout

---

## 9. Feature `checkout`

## 9.1. Vai trò nghiệp vụ

Checkout là nơi:

1. người dùng nhập thông tin giao hàng
2. chọn phương thức thanh toán
3. xem lại các món đã chọn
4. bấm hoàn tất
5. hệ thống ghi vào `orders` và `order_items`
6. xóa đúng các món đã đặt khỏi `cart_items`

### 9.2. View `checkout.php`

File: `backend/app/views/users/pages/checkout.php`

#### 9.2.1. Bố cục

Trang chia 2 cột:

1. trái là form giao hàng
2. phải là tóm tắt đơn hàng

#### 9.2.2. Các field form

1. `address`
2. `full_name`
3. `phone`
4. `payment_method`
5. hidden `selected_items`

`selected_items` là mắt xích rất quan trọng.

Nó đảm bảo:

1. checkout biết user đang đặt những product nào
2. khi submit lại form vì lỗi validation, danh sách sản phẩm vẫn còn nguyên

#### 9.2.3. Phương thức thanh toán

UI đang có:

1. COD
2. ví Momo

Nhưng trong DB hiện tại:

1. bảng `orders.payment_method` dùng enum `COD`, `Bank_Transfer`

Cho nên implementation đang map:

1. nhãn hiển thị “Thanh toán qua ví Momo”
2. giá trị lưu xuống DB là `Bank_Transfer`

Đây là quyết định tạm thời để khớp schema đang có.

### 9.3. Controller `checkout()` và `placeOrder()`

Hai method này cùng nằm trong `CartController`.

#### `checkout()`

Vai trò:

1. nhận `items` từ query string
2. lấy các item tương ứng từ `OrderModel`
3. đổ sẵn thông tin session vào form:
   - address
   - full name
   - phone
4. set payment mặc định là `COD`

Điều này giúp:

1. user không phải nhập lại hoàn toàn từ đầu nếu session đã có dữ liệu

#### `placeOrder()`

Đây là method ghi đơn hàng.

Logic chi tiết:

1. đọc `selected_items`
2. lấy lại danh sách item hợp lệ từ database
3. nếu không còn item hợp lệ thì redirect về cart
4. đọc form data
5. kiểm tra field rỗng
6. kiểm tra payment method hợp lệ
7. tách tên bằng `splitFullName()`
8. gọi `OrderModel::createOrderFromCartItems()`
9. nếu thất bại thì render lại checkout và giữ nguyên dữ liệu user đã nhập
10. nếu thành công thì:
    - cập nhật session profile
    - flash success
    - redirect về cart

### 9.4. Helper `splitFullName()`

Đây là helper nhỏ nhưng cần hiểu rõ.

Nó:

1. tách chuỗi full name theo khoảng trắng
2. lấy từ cuối cùng làm `first_name`
3. phần còn lại ghép thành `last_name`

Ví dụ:

`Nguyễn Văn An`

Sẽ trở thành:

1. `first_name = "An"`
2. `last_name = "Nguyễn Văn"`

Lý do:

1. form checkout hiện chỉ có một ô `Họ và tên`
2. schema database lại tách `first_name` và `last_name`

Đây là cách tách đơn giản, đủ dùng cho hiện tại.

### 9.5. Model `OrderModel.php`

File: `backend/app/models/OrderModel.php`

Model này chuyên xử lý dữ liệu checkout và order.

#### `getCheckoutItems(int $customerId, array $productIds = []): array`

Vai trò:

1. tìm cart của customer
2. lấy item từ `cart_items`
3. join `products`
4. join `product_categories`
5. nếu có `productIds` thì chỉ lấy các món được chọn
6. nếu `productIds` rỗng thì lấy toàn bộ cart

Ý nghĩa:

1. method này dùng được cho cả `GET /checkout` và `POST /checkout`
2. controller luôn lấy dữ liệu mới từ DB, không tin hoàn toàn dữ liệu phía client

#### `createOrderFromCartItems(...)`

Đây là method quan trọng nhất của toàn bộ flow mua hàng.

Nó làm:

1. xác định `cartId`
2. lấy lại các item cần checkout
3. tính `totalPrice`
4. bắt đầu transaction
5. insert vào bảng `orders`
6. lặp qua từng item để insert `order_items`
7. xóa đúng các item vừa checkout khỏi `cart_items`
8. commit transaction
9. nếu lỗi thì rollback

Đây là lý do checkout an toàn hơn việc viết rời rạc.

Nếu không dùng transaction, có thể xảy ra tình huống:

1. đã tạo order
2. nhưng chưa tạo xong order_items
3. hoặc chưa xóa cart

Khi đó dữ liệu sẽ lệch.

Transaction giải quyết đúng vấn đề này.

### 9.6. Luồng ghi order xuống DB

```text
User submit form checkout
-> CartController::placeOrder()
-> OrderModel::getCheckoutItems()
-> validate form
-> OrderModel::createOrderFromCartItems()
-> begin transaction
-> insert orders
-> insert order_items
-> delete selected cart_items
-> commit
-> redirect /cart với flash success
```

---

## 10. Các bảng database liên quan

### 10.1. `products`

Lưu:

1. tên món
2. giá
3. ảnh
4. trạng thái
5. category

### 10.2. `product_categories`

Lưu tên danh mục đồ uống.

### 10.3. `carts`

Mỗi customer có một cart.

### 10.4. `cart_items`

Lưu:

1. `Cart_ID`
2. `Product_ID`
3. `quantity`

Đây là bảng trung gian nhiều-nhiều giữa giỏ hàng và sản phẩm.

### 10.5. `orders`

Lưu thông tin cấp đơn hàng:

1. khách hàng
2. số điện thoại
3. tên
4. địa chỉ giao
5. tổng tiền
6. phương thức thanh toán
7. trạng thái đơn

### 10.6. `order_items`

Lưu chi tiết từng món trong đơn:

1. `Order_ID`
2. `Product_ID`
3. `quantity`
4. `price_at_purchase`

`price_at_purchase` rất quan trọng vì:

1. giá sản phẩm có thể thay đổi về sau
2. nhưng đơn hàng cũ phải lưu đúng giá tại thời điểm đặt

---

## 11. Luồng end-to-end từ đầu đến cuối

## 11.1. Luồng 1: user thêm món từ product modal

1. User vào `/san-pham`
2. `ProductController::menu()` lấy danh sách active products
3. `san-pham.php` render card và gắn `data-*`
4. User click card
5. `san-pham.js` mở modal
6. User tăng giảm số lượng
7. User click `THÊM VÀO GIỎ`
8. JS gọi `POST /cart/add`
9. `CartController::add()` validate login và product
10. `CartModel::addItem()` ghi DB
11. Response JSON trả về
12. Modal hiện feedback thành công

## 11.2. Luồng 2: user thêm món từ collection

1. User hover flip-card
2. Card lật sang mặt sau
3. User click `Đặt ngay`
4. `san-pham.js` gọi cùng endpoint `POST /cart/add`
5. Backend xử lý như luồng modal
6. Nút đổi tạm sang `Dang them...` rồi `Da them`

## 11.3. Luồng 3: user chỉnh giỏ hàng

1. User vào `/cart`
2. `CartController::index()` lấy cart items
3. `cart.php` render HTML và URL endpoint vào `data-*`
4. User sửa số lượng hoặc bấm xóa
5. `cart.js` gọi AJAX tương ứng
6. Backend sửa DB
7. Frontend cập nhật lại DOM và tổng tiền

## 11.4. Luồng 4: user thanh toán

1. User tick chọn một số item trong cart
2. User bấm `THANH TOAN`
3. `cart.js` redirect sang `/checkout?items=...`
4. `CartController::checkout()` lấy đúng item được chọn
5. `checkout.php` hiển thị form và summary
6. User điền thông tin
7. User submit
8. `CartController::placeOrder()` validate
9. `OrderModel::createOrderFromCartItems()` insert `orders` và `order_items`
10. Backend xóa đúng item đã checkout khỏi cart
11. User bị redirect về `/cart` với flash success

---

## 12. Các quyết định thiết kế đáng chú ý

### 12.1. Vì sao dùng AJAX ở `add cart`, `update cart`, `remove cart`

Lý do:

1. user cần phản hồi ngay
2. thao tác nhỏ, không cần reload cả trang
3. trải nghiệm dùng sản phẩm và giỏ hàng tự nhiên hơn

### 12.2. Vì sao checkout vẫn dùng form `POST` truyền thống

Lý do:

1. đây là bước tạo dữ liệu thật trong DB
2. form truyền thống đơn giản và ổn định
3. dễ giữ dữ liệu nhập lại nếu validation fail
4. phù hợp triết lý “JS tối giản”

### 12.3. Vì sao truyền dữ liệu qua `data-*` trong HTML

Lý do:

1. không cần API “product detail” riêng
2. JS có thể đọc dữ liệu ngay từ DOM
3. code dễ hiểu với người mới
4. giảm số request phụ

### 12.4. Vì sao cart model có `findOrCreateCartIdByCustomerId()`

Lý do:

1. database có trigger tạo cart
2. nhưng code không nên phụ thuộc tuyệt đối vào trigger
3. model tự đảm bảo “có cart thì mới làm tiếp”

### 12.5. Vì sao không dùng `stock_quantity`

Theo nghiệp vụ hiện tại:

1. đây là đồ ăn / đồ uống
2. khách đặt đến đâu, quán làm đến đó
3. nên không cần chặn theo tồn kho

Vì vậy:

1. DB vẫn có cột `stock_quantity`
2. nhưng controller cart không kiểm tra cột này

---

## 13. Điểm mạnh của cách hiện thực hiện tại

1. Kiến trúc rõ ràng, dễ đọc với người học PHP thuần
2. Route, controller, model, view tách vai trò khá sạch
3. AJAX chỉ dùng ở nơi thực sự cần
4. View render sẵn dữ liệu nên frontend đơn giản
5. `OrderModel` dùng transaction nên an toàn dữ liệu hơn
6. Collection add-to-cart tái sử dụng đúng logic có sẵn, không tách nhánh phức tạp

---

## 14. Giới hạn và điểm cần lưu ý

### 14.1. `BEST SELLER` và `COLLECTION` chưa phải business data thật

Hiện tại:

1. best seller = 5 sản phẩm đầu tiên
2. collection = 4 sản phẩm đầu tiên

Nghĩa là:

1. đây là cách trình bày UI
2. chưa có cột hoặc bảng để quản trị thật các nhóm này

### 14.2. `FLASH SALE` mới là UI, chưa phải giá bán thật

Hiện tại:

1. flash sale hiển thị giá giảm 10% trong view
2. nhưng cart và order vẫn lấy `products.price`

Nếu muốn đúng nghiệp vụ sale thật, cần thêm:

1. cột hoặc bảng chương trình khuyến mãi
2. logic tính giá thực tế ở backend

### 14.3. Search ở `san-pham` chưa lọc toàn bộ màn hình

Hiện tại chỉ lọc:

1. product grid chính

Chưa lọc:

1. best seller
2. flash sale
3. collection

### 14.4. Chưa có CSRF protection

Các endpoint `POST` hiện chưa có token CSRF.

Đây là việc nên bổ sung nếu đưa hệ thống lên môi trường production.

### 14.5. Chưa có xác nhận xóa item

Nút `Xoa` hiện xóa ngay bằng AJAX.

Ưu điểm:

1. nhanh

Nhược điểm:

1. có thể xóa nhầm nếu click nhầm

### 14.6. Tách `full_name` còn đơn giản

`splitFullName()` hiện:

1. lấy từ cuối làm tên
2. phần còn lại làm họ + tên đệm

Cách này đủ dùng hiện tại nhưng không phải chuẩn cho mọi trường hợp.

### 14.7. Phí vận chuyển chưa có logic thật

Hiện tại UI đang hiển thị:

1. `Phi van chuyen = -`

Nghĩa là:

1. phần shipping fee mới là placeholder giao diện

---

## 15. Nếu muốn mở rộng tiếp thì nên làm gì

### 15.1. Về sản phẩm

1. Tạo bảng hoặc cờ cho `best seller`
2. Tạo bảng hoặc cờ cho `collection`
3. Tạo bảng khuyến mãi thật cho `flash sale`
4. Thêm API hoặc logic admin để chỉnh các nhóm này

### 15.2. Về cart

1. Thêm badge số lượng món ở header
2. Thêm confirm trước khi xóa
3. Thêm mini-cart dropdown
4. Thêm ghi chú món nếu cần

### 15.3. Về checkout

1. Thêm validate số điện thoại chặt hơn
2. Tách riêng “họ”, “tên đệm”, “tên”
3. Tính phí vận chuyển thật
4. Tạo trang cảm ơn sau khi đặt hàng thành công
5. Tạo trang lịch sử đơn hàng cho user

### 15.4. Về bảo mật và kỹ thuật

1. Thêm CSRF token
2. Log lỗi backend chi tiết hơn
3. Tách service layer nếu logic ngày càng phức tạp
4. Thêm test cho các model quan trọng

---

## 16. Gợi ý cách thuyết trình feature này

Nếu dùng tài liệu này để thuyết trình, có thể đi theo thứ tự sau:

### Phần 1: Nói về mục tiêu

1. User xem sản phẩm
2. User thêm món vào giỏ
3. User chỉnh giỏ
4. User checkout
5. Hệ thống ghi đơn hàng

### Phần 2: Nói về kiến trúc

1. PHP thuần MVC
2. Route -> Controller -> Model -> View
3. AJAX chỉ dùng cho thao tác nhỏ

### Phần 3: Nói về trang sản phẩm

1. sản phẩm được lấy từ DB
2. render theo category
3. popup dùng chung cho nhiều khu
4. collection có nút `Đặt ngay` add thẳng vào cart

### Phần 4: Nói về cart

1. cart lấy dữ liệu thật từ `cart_items`
2. update và remove dùng AJAX
3. checkbox dùng để chọn món thanh toán

### Phần 5: Nói về checkout

1. user chỉ checkout các món đã tick
2. backend validate form
3. order và order_items được ghi trong transaction
4. sau khi tạo order, item được xóa khỏi cart

### Phần 6: Nói về điểm mạnh

1. đơn giản
2. dễ hiểu
3. phù hợp PHP thuần
4. dễ mở rộng

### Phần 7: Nói về hạn chế và hướng phát triển

1. flash sale chưa là giá thật
2. best seller và collection chưa có rule riêng
3. chưa có CSRF
4. có thể mở rộng thêm lịch sử đơn hàng, thanh toán online, badge cart

---

## 17. Tóm tắt một câu cho từng feature

### `san-pham`

Trang sản phẩm là nơi render toàn bộ menu đồ uống, gom dữ liệu vào `data-*`, mở popup chi tiết và thêm món vào giỏ bằng AJAX.

### `cart`

Trang giỏ hàng là nơi đọc dữ liệu thật từ `cart_items`, cho phép sửa số lượng, xóa món và chọn món để mang sang checkout.

### `checkout`

Trang checkout là bước xác nhận thông tin giao hàng và dùng transaction để tạo `orders`, `order_items`, đồng thời xóa đúng các món đã thanh toán khỏi giỏ hàng.

---

## 18. Kết luận

Ba feature `san-pham -> cart -> checkout` đang tạo thành một luồng mua hàng hoàn chỉnh theo phong cách PHP thuần:

1. trang sản phẩm hiển thị và chọn món
2. giỏ hàng quản lý món đã chọn
3. checkout ghi đơn hàng thật vào database

Điểm mạnh lớn nhất của cách hiện thực hiện tại là:

1. dễ đọc
2. ít phụ thuộc framework
3. JS tối giản
4. luồng dữ liệu rõ
5. rất phù hợp để học, trình bày, và tiếp tục mở rộng


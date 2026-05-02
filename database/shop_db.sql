-- 1. Tắt kiểm tra khóa ngoại để reset database
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS shop_db;

-- 2. Tạo Database
CREATE DATABASE shop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shop_db;

SET FOREIGN_KEY_CHECKS = 1;
-- -------------------------------------------------------
-- 3. TẠO CÁC BẢNG (Giữ nguyên cấu trúc của bạn)
-- -------------------------------------------------------
-- 1. Bảng USER (Bảng gốc cho Admin và Customer)
CREATE TABLE USERS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    gender VARCHAR(10),
    birth_date DATE,
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    status ENUM('active', 'banned', 'pending', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    image VARCHAR(255) -- Lưu đường dẫn ảnh avatar
);

-- 2. Bảng CUSTOMER (Kế thừa từ USERS)
CREATE TABLE CUSTOMER (
    ID INT PRIMARY KEY,
    loyalty_point INT DEFAULT 0,
    FOREIGN KEY (ID) REFERENCES USERS(ID) ON DELETE CASCADE
);

-- 3. Bảng ADMIN (Kế thừa từ USERS)
CREATE TABLE ADMIN (
    ID INT PRIMARY KEY,
    FOREIGN KEY (ID) REFERENCES USERS(ID) ON DELETE CASCADE
);

-- 4. Bảng NEWS_CATEGORIES
CREATE TABLE NEWS_CATEGORIES (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL
);

-- 5. Bảng NEWS
CREATE TABLE NEWS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    image VARCHAR(255),
    Admin_ID INT,
    N_Cate_ID INT,
	status ENUM('published', 'archived') DEFAULT 'published',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    slug VARCHAR(255) UNIQUE NOT NULL,
    FOREIGN KEY (Admin_ID) REFERENCES ADMIN(ID),
    meta_description VARCHAR(255),
    keywords VARCHAR(255),
    FOREIGN KEY (N_Cate_ID) REFERENCES NEWS_CATEGORIES(ID)
);

-- 6. Bảng COMMENTS
CREATE TABLE COMMENTS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT,
    status ENUM('hidden', 'presented') DEFAULT 'presented',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    User_ID INT,
    News_ID INT,
    parent_comment_id INT DEFAULT NULL,
    FOREIGN KEY (User_ID) REFERENCES USERS(ID),
    FOREIGN KEY (News_ID) REFERENCES NEWS(ID) ON DELETE CASCADE,
    FOREIGN KEY (parent_comment_id) REFERENCES COMMENTS(ID) ON DELETE CASCADE
);



-- 8. Bảng FAQS
CREATE TABLE FAQS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT,
    answer TEXT,
    Admin_ID INT,
    FOREIGN KEY (Admin_ID) REFERENCES ADMIN(ID)
);

-- 9. Bảng CONTACTS (Khách hàng gửi liên hệ)
CREATE TABLE CONTACTS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100),
    content TEXT,
    status ENUM('processing', 'replied') DEFAULT 'processing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10. Bảng trung gian MADE_CONTACTS (Nối Customer - Contact)
CREATE TABLE MADE_CONTACTS (
    Contact_ID INT PRIMARY KEY,
    Customer_ID INT,
    FOREIGN KEY (Contact_ID) REFERENCES CONTACTS(ID),
    FOREIGN KEY (Customer_ID) REFERENCES CUSTOMER(ID)
);

-- 11. Bảng trung gian RELIED_CONTACTS (Nối Admin - Contact)
CREATE TABLE RELIED_CONTACTS (
    Admin_ID INT,
    Contact_ID INT PRIMARY KEY,
    FOREIGN KEY (Admin_ID) REFERENCES ADMIN(ID),
    FOREIGN KEY (Contact_ID) REFERENCES CONTACTS(ID)
);

-- 12. Bảng RELIED_INFOR (Lưu lịch sử phản hồi của Admin)
CREATE TABLE RELIED_INFOR (
    Admin_ID INT,
    reply_content VARCHAR(255),
    reply_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (Admin_ID, reply_content, reply_at), -- Khóa chính kết hợp 3 trường
    FOREIGN KEY (Admin_ID) REFERENCES ADMIN(ID) ON DELETE CASCADE
);

-- 13. Bảng PRODUCT_CATEGORIES
CREATE TABLE PRODUCT_CATEGORIES (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL
);

-- 14. Bảng PRODUCTS
CREATE TABLE PRODUCTS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT,
    image VARCHAR(255),
    status ENUM('active', 'inactive', 'out_of_stock', 'archived') DEFAULT 'active',
    price DECIMAL(15, 2),
    stock_quantity INT DEFAULT 0,
    name VARCHAR(255) NOT NULL,
    P_Cate_ID INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    slug VARCHAR(255) UNIQUE NOT NULL,
    FOREIGN KEY (P_Cate_ID) REFERENCES PRODUCT_CATEGORIES(ID)
);

-- 15. Bảng ORDERS
CREATE TABLE ORDERS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
    Customer_phone VARCHAR(20),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    Shipping_address TEXT,
    Customer_ID INT,
    total_price DECIMAL(15, 2) DEFAULT 0,
    payment_method ENUM('COD', 'Bank_Transfer') DEFAULT 'COD',
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    FOREIGN KEY (Customer_ID) REFERENCES CUSTOMER(ID)
);

-- 16. Bảng ORDER_ITEMS (Chi tiết đơn hàng)
CREATE TABLE ORDER_ITEMS (
    Order_ID INT,
    Product_ID INT,
    quantity INT DEFAULT 1,
    price_at_purchase DECIMAL(15, 2), -- Quan trọng: Lưu giá lúc khách mua
    PRIMARY KEY (Order_ID, Product_ID),
    FOREIGN KEY (Order_ID) REFERENCES ORDERS(ID),
    FOREIGN KEY (Product_ID) REFERENCES PRODUCTS(ID)
);

-- 17. Bảng CARTS
CREATE TABLE CARTS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Customer_ID INT,
    FOREIGN KEY (Customer_ID) REFERENCES CUSTOMER(ID)
);

-- 18. Bảng CART_ITEMS (Chi tiết giỏ hàng)
CREATE TABLE CART_ITEMS (
    Cart_ID INT,
    Product_ID INT,
    quantity INT DEFAULT 1,
    PRIMARY KEY (Cart_ID, Product_ID),
    FOREIGN KEY (Cart_ID) REFERENCES CARTS(ID),
    FOREIGN KEY (Product_ID) REFERENCES PRODUCTS(ID)
);

--    4. TẠO CÁC TRIGGER CẦN THIẾT
DELIMITER $$

-- Trigger 1: Tự động phân loại User & Tạo giỏ hàng
CREATE TRIGGER after_user_insert
AFTER INSERT ON USERS
FOR EACH ROW
BEGIN
    IF NEW.role = 'admin' THEN
        INSERT INTO ADMIN (ID) VALUES (NEW.ID);
    ELSE
        INSERT INTO CUSTOMER (ID, loyalty_point) VALUES (NEW.ID, 0);
        INSERT INTO CARTS (Customer_ID) VALUES (NEW.ID);
    END IF;
END $$

-- Trigger 2: Tích điểm thưởng khi đơn hàng hoàn tất
CREATE TRIGGER update_loyalty_points
AFTER UPDATE ON ORDERS
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND OLD.status <> 'completed' THEN
        UPDATE CUSTOMER 
        SET loyalty_point = loyalty_point + FLOOR(NEW.total_price / 100000)
        WHERE ID = NEW.Customer_ID;
    END IF;
END $$


-- /* ------------------------------------------------------
--    5. STORED PROCEDURE 
-- --------------------------------------------------------- */


CREATE PROCEDURE sp_place_order(
    IN p_customer_id INT,
    IN p_phone VARCHAR(20),
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_address TEXT,
    IN p_payment_method VARCHAR(50)
)
BEGIN
    DECLARE v_cart_id INT;
    DECLARE v_order_id INT;
    DECLARE v_total_price DECIMAL(15,2);
    DECLARE v_has_stock BOOLEAN DEFAULT TRUE;

    -- 1. Tìm ID giỏ hàng dựa trên Customer_ID truyền vào
    SELECT ID INTO v_cart_id FROM CARTS WHERE Customer_ID = p_customer_id;

    -- 2. TÍNH TỔNG TIỀN TRƯỚC (Đây là bước fix lỗi 0.00 của bạn)
    SELECT SUM(ci.quantity * p.price) INTO v_total_price
    FROM CART_ITEMS ci
    JOIN PRODUCTS p ON ci.Product_ID = p.ID
    WHERE ci.Cart_ID = v_cart_id;

    -- 3. Kiểm tra nếu giỏ trống hoặc thiếu hàng
    IF v_total_price IS NULL OR v_total_price = 0 THEN
        SET v_has_stock = FALSE;
    END IF;

    IF EXISTS (
        SELECT 1 FROM CART_ITEMS ci 
        JOIN PRODUCTS p ON ci.Product_ID = p.ID 
        WHERE ci.Cart_ID = v_cart_id AND ci.quantity > p.stock_quantity
    ) THEN
        SET v_has_stock = FALSE;
    END IF;

    -- 4. Thực hiện Transaction
    IF v_has_stock THEN
        START TRANSACTION;

        -- Tạo đơn với total_price đã tính sẵn
        INSERT INTO ORDERS (Customer_ID, Customer_phone, first_name, last_name, Shipping_address, payment_method, status, total_price)
        VALUES (p_customer_id, p_phone, p_first_name, p_last_name, p_address, p_payment_method, 'pending', v_total_price);
        
        SET v_order_id = LAST_INSERT_ID();

        -- Chuyển món từ giỏ sang đơn
        INSERT INTO ORDER_ITEMS (Order_ID, Product_ID, quantity, price_at_purchase)
        SELECT v_order_id, ci.Product_ID, ci.quantity, p.price
        FROM CART_ITEMS ci JOIN PRODUCTS p ON ci.Product_ID = p.ID
        WHERE ci.Cart_ID = v_cart_id;

        -- Trừ kho
        UPDATE PRODUCTS p JOIN CART_ITEMS ci ON p.ID = ci.Product_ID
        SET p.stock_quantity = p.stock_quantity - ci.quantity,
            p.status = IF(p.stock_quantity - ci.quantity <= 0, 'out_of_stock', p.status)
        WHERE ci.Cart_ID = v_cart_id;

        -- Xóa sạch giỏ
        DELETE FROM CART_ITEMS WHERE Cart_ID = v_cart_id;

        COMMIT;
        SELECT v_order_id AS result_status;
    ELSE
        SELECT -1 AS result_status;
    END IF;
END $$

DELIMITER ;

-- /* ------------------------------------------------------
--    6. CHÈN DỮ LIỆU BAN ĐẦU (SEED DATA) 
-- --------------------------------------------------------- */
-- 6.1. NGƯỜI DÙNG (Trigger tự tạo bản ghi trong CUSTOMER, ADMIN và CARTS)
INSERT INTO USERS (first_name, last_name, email, password, role, phone, address) VALUES 
('Minh', 'Quản Trị', 'admin.master@shop.com', 'pass_admin_123', 'admin', '0901111222', '123 Quận 1, TP.HCM'),
('Hoàng', 'Khách', 'hoang.customer@gmail.com', 'pass_hoang_456', 'customer', '0903333444', '456 Quận 7, TP.HCM'),
('Lan', 'Nguyễn', 'lan.nguyen@gmail.com', 'pass_lan_789', 'customer', '0905555666', '789 Quận 3, TP.HCM'),
('Tú', 'Trần', 'tu.tran@gmail.com', 'pass_tu_000', 'customer', '0907777888', '101 Quận Bình Thạnh, TP.HCM');

-- 6.2. TIN TỨC & DANH MỤC TIN TỨC
INSERT INTO NEWS_CATEGORIES (Name) VALUES ('Khuyến mãi'), ('Đánh giá'), ('Xu hướng');

INSERT INTO NEWS (title, slug, Admin_ID, N_Cate_ID, content, status) VALUES 
('Siêu Sale Mùa Hè 2026', 'sieu-sale-mua-he', 1, 1, 'Giảm giá cực sâu lên đến 50% cho tất cả dòng Laptop...', 'published'),
('Review Macbook M3 Pro', 'review-macbook-m3', 1, 2, 'Cấu hình mạnh mẽ, pin trâu, màn hình Liquid Retina cực đẹp...', 'published'),
('Xu hướng Smartphone 2026', 'xu-huong-phone-2026', 1, 3, 'Những đột phá về AI và màn hình cuộn sắp ra mắt...', 'published');

-- 6.3. BÌNH LUẬN & PHẢN HỒI (COMMENTS & RELY)
INSERT INTO COMMENTS (content, User_ID, News_ID, status) VALUES 
('Bài viết review rất tâm huyết, cảm ơn Admin!', 2, 2, 'presented'), -- ID 1
('Mã giảm giá có áp dụng cho phụ kiện không ạ?', 3, 1, 'presented'), -- ID 2
('Admin cho mình hỏi giá iPhone 17 dự kiến bao nhiêu?', 4, 3, 'presented'); -- ID 3

-- Admin phản hồi cho bình luận ID 1
INSERT INTO COMMENTS (content, User_ID, News_ID, status) VALUES 
('Cảm ơn bạn Hoàng đã ủng hộ shop nhé!', 1, 2, 'presented'); -- ID 4

-- 6.4. HỖ TRỢ & LIÊN HỆ (FAQS, CONTACTS, RELIED)
INSERT INTO FAQS (question, answer, Admin_ID) VALUES 
('Shop có hỗ trợ trả góp không?', 'Có, shop hỗ trợ trả góp 0% qua thẻ tín dụng và công ty tài chính.', 1),
('Thời gian giao hàng mất bao lâu?', 'Nội thành TP.HCM giao trong ngày, các tỉnh khác từ 2-3 ngày.', 1);

-- Khách Hoàng gửi liên hệ hỗ trợ
INSERT INTO CONTACTS (first_name, last_name, email, content, status) VALUES 
('Hoàng', 'Khách', 'hoang.customer@gmail.com', 'Mình cần tư vấn thủ tục trả góp cho Macbook M3 Pro.', 'replied');

INSERT INTO MADE_CONTACTS (Contact_ID, Customer_ID) VALUES (1, 2);
INSERT INTO RELIED_CONTACTS (Admin_ID, Contact_ID) VALUES (1, 1);
INSERT INTO RELIED_INFOR (Admin_ID, reply_content) VALUES (1, 'Đã gọi điện tư vấn và gửi mail quy trình trả góp cho khách.');

-- 6.5. DANH MỤC SẢN PHẨM & SẢN PHẨM
INSERT INTO PRODUCT_CATEGORIES (Name) VALUES ('Laptop'), ('Smartphone'), ('Phụ kiện');

INSERT INTO PRODUCTS (name, slug, price, stock_quantity, P_Cate_ID, status) VALUES 
('Macbook M3 Pro', 'mac-m3-pro-2026', 45000000, 10, 1, 'active'), -- ID 1
('iPhone 17 Ultra', 'iphone-17-ultra', 35000000, 20, 2, 'active'), -- ID 2
('AirPods Pro 3', 'airpods-pro-3', 6000000, 50, 3, 'active'), -- ID 3
('Ốp lưng MagSafe', 'op-lung-magsafe', 1200000, 0, 3, 'out_of_stock'); -- ID 4

-- 6.6. GIỎ HÀNG (CART_ITEMS)
-- Khách Hoàng (ID 2) đang có 1 Mac và 2 AirPods trong giỏ
INSERT INTO CART_ITEMS (Cart_ID, Product_ID, quantity) VALUES (2, 1, 1), (2, 3, 2);
-- Khách Lan (ID 3) đang có 1 iPhone trong giỏ
INSERT INTO CART_ITEMS (Cart_ID, Product_ID, quantity) VALUES (3, 2, 1);

SET SQL_SAFE_UPDATES = 0;
-- BƯỚC 1: Tìm ID thực tế của Khách Hoàng và Cart của ông ấy
SET @TargetUser = (SELECT ID FROM USERS WHERE email = 'hoang.customer@gmail.com');
SET @TargetCart = (SELECT ID FROM CARTS WHERE Customer_ID = @TargetUser);

-- BƯỚC 2: Chuẩn bị giỏ hàng (Xóa sạch giỏ cũ cho chắc ăn rồi thêm đồ mới)
DELETE FROM CART_ITEMS WHERE Cart_ID = @TargetCart;
INSERT INTO CART_ITEMS (Cart_ID, Product_ID, quantity) VALUES 
(@TargetCart, 1, 1), -- 1 cái Macbook (45,000,000)
(@TargetCart, 3, 2); -- 2 cái AirPods (6,000,000 x 2 = 12,000,000)

-- BƯỚC 3: GỌI PROCEDURE CHỐT ĐƠN
-- Nó sẽ tự: Tính tổng 57tr -> Tạo Order -> Chuyển Item -> Trừ kho -> Xóa giỏ
CALL sp_place_order(@TargetUser, '0903333444', 'Hoàng', 'Khách', '456 Quận 7, TP.HCM', 'Bank_Transfer');

-- /* ======================================================
--    XEM KẾT QUẢ ĐỂ THẤY TRIGGER, PRODUCED HOẠT ĐỘNG
--    ====================================================== */

-- 1. Xem Đơn hàng (Phải có total_price = 57,000,000)
SELECT '1. ĐƠN HÀNG MỚI' AS Status;
SELECT ID, total_price, status, payment_method FROM ORDERS WHERE Customer_ID = @TargetUser;

-- 2. Xem Kho hàng (Macbook 10 -> 9, AirPods 50 -> 48)
SELECT '2. KHO HÀNG ĐÃ TRỪ' AS Status;
SELECT name, stock_quantity FROM PRODUCTS WHERE ID IN (1, 3);

-- 3. Xem Giỏ hàng (Phải trống trơn)
SELECT '3. GIỎ HÀNG SAU THANH TOÁN' AS Status;
SELECT * FROM CART_ITEMS WHERE Cart_ID = @TargetCart;

-- 4. Test Trigger Tích điểm (Cập nhật đơn hàng thành Completed)
UPDATE ORDERS SET status = 'completed' WHERE Customer_ID = @TargetUser;

-- Xem điểm (57tr -> 570 điểm)
SELECT '4. ĐIỂM THƯỞNG LOYALTY' AS Status;
SELECT u.email, cu.loyalty_point FROM USERS u JOIN CUSTOMER cu ON u.ID = cu.ID WHERE u.ID = @TargetUser;


-- 1. Lấy ID của khách Hoàng
SET @Hoang_ID = (SELECT ID FROM USERS WHERE email = 'hoang.customer@gmail.com');
SET @Hoang_Cart = (SELECT ID FROM CARTS WHERE Customer_ID = @Hoang_ID);

-- 2. Bỏ đồ vào giỏ (1 Mac 45tr, 2 AirPods 12tr => Tổng 57tr)
DELETE FROM CART_ITEMS WHERE Cart_ID = @Hoang_Cart;
INSERT INTO CART_ITEMS (Cart_ID, Product_ID, quantity) VALUES (@Hoang_Cart, 1, 1), (@Hoang_Cart, 3, 2);

-- 3. Gọi Procedure
CALL sp_place_order(@Hoang_ID, '090333', 'Hoàng', 'Khách', 'HCM', 'Bank_Transfer');

-- 4. Kiểm tra kết quả
SELECT 'TEST 1: THANH TOÁN THÀNH CÔNG' AS Status;
SELECT * FROM ORDERS WHERE Customer_ID = @Hoang_ID; -- Check total_price = 57,000,000
SELECT name, stock_quantity FROM PRODUCTS WHERE ID IN (1, 3); -- Check kho giảm
SELECT * FROM CART_ITEMS WHERE Cart_ID = @Hoang_Cart; -- Check giỏ trống

-- -------------------------------------------------------
-- 7. CÂU LỆNH IN RA KIỂM TRA (TEST)
-- -------------------------------------------------------
-- Lệnh in nhanh toàn bộ các bảng chính để test
SELECT '--- USERS ---' AS Info; SELECT * FROM USERS;
SELECT '--- CUSTOMERS ---' AS Info; SELECT * FROM CUSTOMER;
SELECT '--- ADMINS ---' AS Info; SELECT * FROM ADMIN;
SELECT '--- PRODUCTS ---' AS Info; SELECT * FROM PRODUCTS;
SELECT '--- NEWS ---' AS Info; SELECT * FROM NEWS;
SELECT '--- ORDERS ---' AS Info; SELECT * FROM ORDERS;
SELECT '--- ORDER ITEMS ---' AS Info; SELECT * FROM ORDER_ITEMS;
SELECT '--- CARTS ---' AS Info; SELECT * FROM CARTS;
SELECT '--- CONTACTS ---' AS Info; SELECT * FROM CONTACTS;
SELECT '--- RELIED_INFOR ---' AS Info; SELECT * FROM RELIED_INFOR;



SET SQL_SAFE_UPDATES = 1;
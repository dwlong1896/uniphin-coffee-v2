-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 10, 2026 lúc 05:42 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shop_db2`
--

DELIMITER $$
--
-- Thủ tục
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_place_order` (IN `p_customer_id` INT, IN `p_phone` VARCHAR(20), IN `p_first_name` VARCHAR(50), IN `p_last_name` VARCHAR(50), IN `p_address` TEXT, IN `p_payment_method` VARCHAR(50))   BEGIN
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `about_sections`
--

CREATE TABLE `about_sections` (
  `id` int(11) NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT '',
  `content` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `about_sections`
--

INSERT INTO `about_sections` (`id`, `section_key`, `title`, `content`, `image_url`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'hero', 'NƠI BẮT ĐẦU NHỮNG NGÀY HIỆU QUẢ NHẤT', 'Một không gian yên tĩnh, thoải mái cùng ly cà phê chất lượng giúp bạn khởi đầu ngày mới với sự tập trung và năng lượng, để học tập và làm việc hiệu quả hơn.', '', 1, '2026-05-10 02:23:56', '2026-05-10 02:25:21'),
(2, 'origin', 'Nguồn gốc', 'UniPhin Coffee bắt đầu từ một ý tưởng rất đơn giản – tạo ra một không gian dành riêng cho sinh viên, nơi ai cũng có thể tìm thấy sự yên tĩnh giữa nhịp sống bận rộn. Xuất phát từ những trải nghiệm học tập và làm việc chưa trọn vẹn, chúng mình mong muốn xây dựng một \"góc nhỏ\" đủ thoải mái để mỗi người có thể tập trung và phát triển bản thân.\r\n\r\nTừ những bước đầu đó, UniPhin Coffee ra đời với tinh thần gần gũi, giản dị và luôn hướng đến cộng đồng sinh viên.', '', 2, '2026-05-10 02:23:56', '2026-05-10 02:23:56'),
(3, 'mission', 'Sứ mệnh', 'UniPhin Coffee hướng đến việc mang lại những ly cà phê chất lượng với mức giá phù hợp cho sinh viên, đi cùng với đó là một không gian học tập và làm việc hiệu quả.\r\n\r\nChúng mình mong muốn tạo ra một môi trường yên tĩnh, thoải mái, nơi bạn có thể tập trung, hoàn thành công việc và phát triển bản thân mỗi ngày. UniPhin không chỉ phục vụ đồ uống, mà còn đồng hành cùng bạn trong hành trình học tập và làm việc.', '', 3, '2026-05-10 02:23:56', '2026-05-10 02:23:56'),
(4, 'quality', 'Từ hạt cà phê ngon đến ly cà phê trọn vị nhất', 'Chúng mình lựa chọn nguồn cà phê từ những vùng trồng tại Việt Nam, nơi điều kiện tự nhiên và sự chăm sóc của người nông dân tạo nên những hạt cà phê chất lượng. Từng hạt được rang xay cẩn thận, giữ lại hương vị tự nhiên – đậm vừa đủ, dễ uống và phù hợp với nhiều người, đặc biệt là sinh viên.\r\n\r\nVới UniPhin, giá trị không chỉ nằm ở hương vị, mà còn ở cảm giác bạn nhận được khi thưởng thức. Đó có thể là một chút tỉnh táo để tiếp tục công việc, hay đơn giản là một khoảnh khắc dễ chịu giữa ngày dài.', '', 4, '2026-05-10 02:23:56', '2026-05-10 02:26:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`ID`) VALUES
(1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `ID` int(11) NOT NULL,
  `Customer_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`ID`, `Customer_ID`) VALUES
(4, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `Cart_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `ID` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `status` enum('hidden','presented') DEFAULT 'presented',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `User_ID` int(11) DEFAULT NULL,
  `News_ID` int(11) DEFAULT NULL,
  `parent_comment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`ID`, `content`, `status`, `created_at`, `User_ID`, `News_ID`, `parent_comment_id`) VALUES
(1, 'Bài viết review rất tâm huyết, cảm ơn Admin!', 'presented', '2026-05-10 01:23:22', 2, 2, NULL),
(4, 'Cảm ơn bạn Hoàng đã ủng hộ shop nhé!', 'presented', '2026-05-10 01:23:22', 1, 2, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `ID` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` enum('processing','replied') DEFAULT 'processing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`ID`, `first_name`, `last_name`, `email`, `content`, `status`, `created_at`) VALUES
(1, 'Hoàng', 'Khách', 'hoang.customer@gmail.com', 'Mình cần tư vấn thủ tục trả góp cho Macbook M3 Pro.', 'replied', '2026-05-10 01:23:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer`
--

CREATE TABLE `customer` (
  `ID` int(11) NOT NULL,
  `loyalty_point` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customer`
--

INSERT INTO `customer` (`ID`, `loyalty_point`) VALUES
(2, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Thời gian giao hàng là bao lâu?', 'Chúng tôi hỗ trợ giao hàng nhanh trong ngày đối với khu vực nội thành (thường từ 30 phút – 2 giờ tùy khu vực).', 1, 1, '2026-05-10 02:23:56', '2026-05-10 02:23:56'),
(2, 'Làm thế nào để có thể đặt hàng trên website?', 'Bạn chỉ cần chọn sản phẩm, thêm vào giỏ hàng và tiến hành thanh toán. Hệ thống sẽ xác nhận đơn hàng qua email.', 2, 1, '2026-05-10 02:23:56', '2026-05-10 02:23:56'),
(3, 'Shop có hỗ trợ trả góp không?', 'Có, shop hỗ trợ trả góp 0% qua thẻ tín dụng và công ty tài chính.', 3, 1, '2026-05-10 02:23:56', '2026-05-10 02:23:56'),
(4, 'Chính sách đổi trả như thế nào?', 'Sản phẩm được đổi trả trong vòng 7 ngày nếu còn nguyên tem mác và chưa qua sử dụng.', 4, 1, '2026-05-10 02:23:56', '2026-05-10 02:23:56'),
(5, 'Có chương trình khuyến mãi cho sinh viên không?', 'Có, chúng tôi thường xuyên có chương trình giảm giá dành riêng cho sinh viên khi xuất trình thẻ sinh viên.', 5, 1, '2026-05-10 02:23:56', '2026-05-10 02:23:56'),
(6, 'Quán mở cửa từ mấy giờ?', 'Quán mở cửa từ 7:00 sáng đến 22:00 tối hàng ngày, kể cả cuối tuần và ngày lễ.', 6, 1, '2026-05-10 02:23:56', '2026-05-10 02:23:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `made_contacts`
--

CREATE TABLE `made_contacts` (
  `Contact_ID` int(11) NOT NULL,
  `Customer_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `made_contacts`
--

INSERT INTO `made_contacts` (`Contact_ID`, `Customer_ID`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news`
--

CREATE TABLE `news` (
  `ID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `Admin_ID` int(11) DEFAULT NULL,
  `N_Cate_ID` int(11) DEFAULT NULL,
  `status` enum('published','archived') DEFAULT 'published',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `slug` varchar(255) NOT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `news`
--

INSERT INTO `news` (`ID`, `created_at`, `title`, `content`, `image`, `Admin_ID`, `N_Cate_ID`, `status`, `updated_at`, `slug`, `meta_description`, `keywords`) VALUES
(1, '2026-05-10 01:23:22', 'Siêu Sale Mùa Hè 2026', 'Giảm giá cực sâu lên đến 50% cho tất cả dòng Laptop...', NULL, 1, 1, 'published', '2026-05-10 01:23:22', 'sieu-sale-mua-he', NULL, NULL),
(2, '2026-05-10 01:23:22', 'Review Macbook M3 Pro', 'Cấu hình mạnh mẽ, pin trâu, màn hình Liquid Retina cực đẹp...', NULL, 1, 2, 'published', '2026-05-10 01:23:22', 'review-macbook-m3', NULL, NULL),
(3, '2026-05-10 01:23:22', 'Xu hướng Smartphone 2026', 'Những đột phá về AI và màn hình cuộn sắp ra mắt...', NULL, 1, 3, 'published', '2026-05-10 01:23:22', 'xu-huong-phone-2026', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news_categories`
--

CREATE TABLE `news_categories` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `news_categories`
--

INSERT INTO `news_categories` (`ID`, `Name`) VALUES
(1, 'Khuyến mãi'),
(2, 'Đánh giá'),
(3, 'Xu hướng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `ID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','confirmed','shipping','completed','cancelled') DEFAULT 'pending',
  `Customer_phone` varchar(20) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `Shipping_address` text DEFAULT NULL,
  `Customer_ID` int(11) DEFAULT NULL,
  `total_price` decimal(15,2) DEFAULT 0.00,
  `payment_method` enum('COD','Bank_Transfer') DEFAULT 'COD',
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`ID`, `created_at`, `status`, `Customer_phone`, `first_name`, `last_name`, `Shipping_address`, `Customer_ID`, `total_price`, `payment_method`, `payment_status`) VALUES
(1, '2026-05-10 03:38:42', 'pending', '0903333444', 'Khách', 'Hoàng', '456 Quận 7, TP.HCM', 2, 65000.00, 'COD', 'unpaid');

--
-- Bẫy `orders`
--
DELIMITER $$
CREATE TRIGGER `update_loyalty_points` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    IF NEW.status = 'completed' AND OLD.status <> 'completed' THEN
        UPDATE CUSTOMER 
        SET loyalty_point = loyalty_point + FLOOR(NEW.total_price / 100000)
        WHERE ID = NEW.Customer_ID;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `Order_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price_at_purchase` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`Order_ID`, `Product_ID`, `quantity`, `price_at_purchase`) VALUES
(1, 113, 1, 30000.00),
(1, 124, 1, 35000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `ID` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','out_of_stock','archived') DEFAULT 'active',
  `price` decimal(15,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `P_Cate_ID` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`ID`, `description`, `image`, `status`, `price`, `stock_quantity`, `name`, `P_Cate_ID`, `updated_at`, `slug`) VALUES
(101, 'Sự kết hợp hài hòa giữa cà phê rang xay đậm vị cùng lớp kem sữa béo nhẹ tạo nên hương vị đặc trưng cho A-Mê Mơ. Thức uống mang đến cảm giác thơm nồng của cà phê truyền thống hòa quyện cùng hậu vị ngọt dịu, phù hợp cho những ai yêu thích sự cân bằng giữa vị đắng và béo. Đây là lựa chọn lý tưởng để bắt đầu ngày mới hoặc thư giãn sau những giờ học tập và làm việc căng thẳng.', 'product-1-1778233430.png', 'active', 35000.00, 9, 'A-Mê Mơ', 11, '2026-05-08 03:28:36', 'a-me-mo'),
(103, 'A-Mê Đào là sự kết hợp độc đáo giữa cà phê nguyên chất và hương đào thanh mát, mang lại trải nghiệm mới lạ nhưng vẫn dễ uống. Vị chua nhẹ tự nhiên của đào giúp cân bằng vị cà phê, tạo cảm giác tươi mới và sảng khoái. Thức uống phù hợp cho những ngày hè nóng bức hoặc dành cho khách hàng muốn khám phá hương vị cà phê trái cây đầy sáng tạo.', 'product-3-1778233353.png', 'active', 40000.00, 48, 'A-Mê Đào', 11, '2026-05-08 02:42:33', 'a-me-dao'),
(111, 'A-Mê Yuzu nổi bật với sự hòa quyện giữa cà phê đậm đà và hương yuzu thơm dịu đặc trưng của Nhật Bản. Vị chua thanh tự nhiên kết hợp cùng hậu vị cà phê tạo nên cảm giác tươi mát và dễ chịu. Đây là thức uống dành cho những ai yêu thích sự mới mẻ, nhẹ nhàng nhưng vẫn giữ được nét đặc trưng của cà phê hiện đại.', 'product-11-1778233558.png', 'active', 30000.00, 0, 'A-Mê Yuzu', 11, '2026-05-08 03:28:53', 'a-me-yuzu'),
(112, 'A-Mê Classic mang phong cách cà phê truyền thống với hương thơm đậm đà và vị cà phê nguyên bản. Thức uống được pha chế từ hạt cà phê chất lượng, tạo nên hậu vị sâu và dễ gây ấn tượng ngay từ lần đầu thưởng thức. Đây là lựa chọn phù hợp cho những khách hàng yêu thích hương vị cà phê thuần túy và quen thuộc.', 'product-12-1778227622.png', 'active', 33000.00, 0, 'A-Mê Classic', 11, '2026-05-08 01:07:02', 'a-me-classic'),
(113, 'A-Mê Vải là sự kết hợp giữa cà phê rang xay cùng hương vải ngọt thanh, mang đến trải nghiệm vừa mới lạ vừa dễ uống. Hương thơm tự nhiên của vải giúp thức uống thêm phần tươi mát, tạo cảm giác nhẹ nhàng và thư giãn. Đây là lựa chọn phù hợp cho những ai yêu thích các dòng cà phê trái cây hiện đại.', 'product-13-1778088319.png', 'active', 30000.00, 0, 'A-Mê Vải', 11, '2026-05-06 10:25:52', 'a-me-vai'),
(114, 'Cold Brew Truyền Thống được ủ lạnh trong nhiều giờ để chiết xuất trọn vẹn hương vị cà phê mà không tạo cảm giác quá đắng. Thức uống mang vị cà phê mượt mà, hậu vị dịu nhẹ và ít chua hơn so với cách pha thông thường. Đây là lựa chọn hoàn hảo cho những ai yêu thích cà phê lạnh với hương vị nguyên bản và dễ thưởng thức.', 'product-14-1778233745.png', 'active', 35000.00, 0, 'Cold Brew Truyền Thống', 14, '2026-05-08 02:49:05', 'cold-brew-truyen-thong'),
(115, 'Cold Brew Kim Quất mang đến sự kết hợp hài hòa giữa vị cà phê cold brew dịu nhẹ và vị chua thanh tự nhiên của kim quất. Hương thơm tươi mát giúp thức uống trở nên sảng khoái, dễ uống và phù hợp trong những ngày thời tiết nóng bức. Đây là lựa chọn độc đáo dành cho khách hàng yêu thích sự sáng tạo trong hương vị cà phê.', 'product-15-1778233835.png', 'active', 33000.00, 0, 'Cold Brew Kim Quất', 14, '2026-05-08 02:50:35', 'cold-brew-kim-quat'),
(116, 'Cappucino Đá là sự hòa quyện giữa espresso đậm vị cùng lớp sữa béo mịn, tạo nên hương vị cân bằng và dễ uống. Lớp bọt sữa mềm mượt kết hợp cùng đá lạnh mang lại cảm giác tươi mát nhưng vẫn giữ được độ thơm đặc trưng của cà phê Ý. Đây là thức uống phù hợp cho những ai yêu thích cà phê sữa hiện đại.', 'product-16-1778233999.png', 'active', 28000.00, 0, 'Cappucino Đá', 16, '2026-05-08 02:53:19', 'cappucino-da'),
(117, 'Cappucino Nóng mang hương vị cà phê Ý truyền thống với lớp sữa nóng béo nhẹ và bọt sữa mịn màng. Vị espresso đậm đà hòa quyện cùng sữa tạo nên cảm giác ấm áp và dễ chịu trong từng ngụm uống. Đây là lựa chọn thích hợp cho những buổi sáng nhẹ nhàng hoặc khi cần thư giãn.', 'product-17-1778234197.png', 'active', 28000.00, 0, 'Cappucino Nóng', 16, '2026-05-08 02:56:37', 'cappucino-nong'),
(118, 'Caramel Macchiato Đá là sự kết hợp giữa espresso, sữa tươi và sốt caramel ngọt dịu. Thức uống mang vị béo nhẹ của sữa hòa cùng hương caramel thơm ngọt, tạo cảm giác dễ uống và hấp dẫn. Phiên bản đá lạnh giúp tăng thêm sự sảng khoái, phù hợp cho mọi thời điểm trong ngày.', 'product-18-1778234340.png', 'active', 30000.00, 0, 'Caramel Macchiato Đá', 16, '2026-05-08 02:59:00', 'caramel-macchiato-da'),
(119, 'Caramel Macchiato Nóng mang đến trải nghiệm cà phê ấm áp với hương caramel thơm ngọt hòa quyện cùng espresso đậm vị. Lớp sữa nóng mềm mịn giúp cân bằng vị cà phê, tạo nên thức uống vừa tinh tế vừa dễ thưởng thức. Đây là lựa chọn hoàn hảo cho những ai yêu thích hương vị ngọt nhẹ và sang trọng.', 'product-19-1778234459.png', 'active', 30000.00, 0, 'Caramel Macchiato Nóng', 16, '2026-05-08 03:00:59', 'caramel-macchiato-nong'),
(120, 'Espresso Nóng được pha từ hạt cà phê rang xay nguyên chất bằng áp suất cao, mang đến lớp crema đẹp mắt cùng hương vị mạnh mẽ đặc trưng. Vị cà phê đậm đà và hậu vị kéo dài giúp đánh thức mọi giác quan. Đây là lựa chọn dành cho những người yêu thích cà phê nguyên bản và đầy năng lượng.', 'product-20-1778234493.png', 'active', 32000.00, 0, 'Espresso Nóng', 16, '2026-05-08 03:01:33', 'espresso-nong'),
(121, 'Espresso Đá giữ nguyên hương vị mạnh mẽ đặc trưng của espresso nhưng được phục vụ cùng đá lạnh để tạo cảm giác sảng khoái hơn khi thưởng thức. Vị cà phê đậm, thơm nồng và hậu vị kéo dài mang đến nguồn năng lượng tức thì cho ngày mới. Đây là lựa chọn phù hợp cho những khách hàng yêu thích cà phê nguyên chất theo phong cách hiện đại.', 'product-21-1778234564.png', 'active', 32000.00, 0, 'Espresso Đá', 16, '2026-05-08 03:02:44', 'espresso-da'),
(122, 'Frappe Almond là thức uống đá xay thơm béo với sự kết hợp giữa sữa, cà phê và hương hạnh nhân đặc trưng. Kết cấu mịn lạnh cùng hương thơm dịu nhẹ mang lại cảm giác dễ chịu và thư giãn khi thưởng thức. Đây là lựa chọn lý tưởng cho những ai yêu thích các dòng đồ uống đá xay béo nhẹ và thơm ngậy.', 'product-22-1778234669.png', 'active', 35000.00, 0, 'Frappe Almod', 17, '2026-05-08 03:04:29', 'frappe-almond'),
(123, 'Frappe Choco Chip mang hương vị chocolate đậm đà kết hợp cùng đá xay mát lạnh và những mảnh choco chip giòn nhẹ hấp dẫn. Thức uống tạo cảm giác ngọt ngào, béo nhẹ và phù hợp với nhiều đối tượng khách hàng. Đây là lựa chọn hoàn hảo cho những ai yêu thích vị chocolate trong các món đồ uống hiện đại.', 'product-23-1778234769.png', 'active', 35000.00, 0, 'Frappe Choco Chip', 17, '2026-05-08 03:06:09', 'frappe-choco-chip'),
(124, 'Frappe Matcha là sự kết hợp giữa bột matcha thơm dịu cùng sữa và đá xay mịn, tạo nên thức uống thanh mát và dễ uống. Vị trà xanh nhẹ nhàng hòa quyện cùng độ béo vừa phải mang đến cảm giác thư giãn và tươi mới. Đây là lựa chọn phù hợp cho những khách hàng yêu thích hương vị matcha truyền thống.', 'product-24-1778234858.png', 'active', 35000.00, 0, 'Frappe Matcha', 17, '2026-05-08 03:07:38', 'frappe-matcha'),
(125, 'Frappe Hazzelnut nổi bật với hương hạt phỉ thơm béo hòa quyện cùng lớp đá xay mịn lạnh đầy hấp dẫn. Thức uống mang vị ngọt nhẹ, dễ uống và phù hợp cho cả những khách hàng không uống cà phê quá đậm. Đây là lựa chọn hoàn hảo để giải nhiệt và thư giãn trong những ngày nóng bức.', 'product-25-1778234912.png', 'active', 35000.00, 0, 'Frappe Hazzelnut', 17, '2026-05-08 03:08:32', 'frappe-hazelnut'),
(126, 'Latte Matcha là sự hòa quyện giữa trà xanh matcha nguyên chất và sữa tươi béo nhẹ, tạo nên hương vị thanh mát và dễ chịu. Màu xanh đặc trưng cùng lớp sữa mềm mịn giúp thức uống trở nên hấp dẫn cả về hương vị lẫn hình thức. Đây là lựa chọn phù hợp cho những ai yêu thích sự nhẹ nhàng và tinh tế.', 'product-26-1778234966.png', 'active', 33000.00, 0, 'Latte Matcha', 13, '2026-05-08 03:09:26', 'latte-matcha'),
(127, 'Latte Classic mang phong cách đơn giản nhưng tinh tế với sự kết hợp giữa espresso và sữa tươi nóng hoặc lạnh. Hương vị cà phê nhẹ nhàng hòa quyện cùng độ béo vừa phải tạo nên cảm giác dễ uống và thư giãn. Đây là thức uống phù hợp cho cả người mới bắt đầu uống cà phê.', 'product-27-1778235063.png', 'active', 30000.00, 0, 'Latte Classic', 13, '2026-05-08 03:11:03', 'latte-classic'),
(128, 'Latte Tiramisu lấy cảm hứng từ món bánh tiramisu nổi tiếng với hương thơm cacao và cà phê nhẹ nhàng. Sự kết hợp giữa sữa béo cùng vị ngọt dịu tạo nên thức uống mềm mại và đầy cuốn hút. Đây là lựa chọn lý tưởng cho những khách hàng yêu thích các món đồ uống mang phong cách tráng miệng.', 'product-28-1778235146.png', 'active', 30000.00, 0, 'Latte Tiramisu', 13, '2026-05-08 03:12:26', 'latte-tiramisu'),
(129, 'Latte Coconut là sự kết hợp hài hòa giữa cà phê latte truyền thống và hương dừa thơm béo tự nhiên. Thức uống mang cảm giác nhiệt đới tươi mới, vị ngọt nhẹ và dễ uống trong mọi thời điểm. Đây là lựa chọn phù hợp cho những ai muốn trải nghiệm sự khác biệt trong các dòng latte hiện đại.', 'product-29-1778235263.png', 'active', 28000.00, 0, 'Latte Coconut', 13, '2026-05-08 03:14:23', 'latte-coconut'),
(130, 'Latte Matcha Hạch Nhân là sự kết hợp độc đáo giữa matcha thanh nhẹ, sữa béo và hương hạnh nhân thơm dịu. Thức uống mang đến cảm giác mềm mại, cân bằng giữa vị trà xanh và độ béo tự nhiên của hạt. Đây là lựa chọn hoàn hảo cho khách hàng yêu thích các món uống healthy và hiện đại.', 'product-30-1778235330.png', 'active', 32000.00, 0, 'Latte Matcha Hạch Nhân', 13, '2026-05-08 03:15:30', 'latte-matcha-hach-nhan'),
(131, 'Matcha Đào là sự kết hợp giữa trà xanh matcha thơm dịu và vị đào thanh ngọt tự nhiên, tạo nên thức uống tươi mát và đầy màu sắc. Vị trà xanh nhẹ hòa quyện cùng hậu vị trái cây mang lại cảm giác dễ chịu khi thưởng thức. Đây là lựa chọn phù hợp cho những ngày hè hoặc những ai yêu thích đồ uống trái cây kết hợp matcha.', 'product-31-1778235434.png', 'active', 35000.00, 0, 'Matcha Đào', 13, '2026-05-08 03:17:14', 'matcha-dao'),
(132, 'Latte Matcha Nóng mang đến cảm giác ấm áp với hương matcha thơm dịu hòa quyện cùng lớp sữa nóng mềm mịn. Vị trà xanh thanh nhẹ kết hợp cùng độ béo vừa phải tạo nên trải nghiệm thư giãn và dễ chịu. Đây là thức uống thích hợp cho những buổi sáng nhẹ nhàng hoặc thời tiết se lạnh.', 'product-32-1778235617.png', 'active', 33000.00, 0, 'Latte Matcha Nóng', 13, '2026-05-08 03:20:17', 'latte-matcha-nong'),
(133, 'Cà Phê Phin Đen Nóng được pha theo phong cách truyền thống Việt Nam với hương vị đậm đà và thơm nồng đặc trưng. Từng giọt cà phê chậm rãi mang lại cảm giác nguyên bản và đầy chiều sâu khi thưởng thức. Đây là lựa chọn dành cho những người yêu thích cà phê mạnh và truyền thống.', 'product-33-1778235708.png', 'active', 25000.00, 0, 'Cà Phê Phin Đen Nóng', 12, '2026-05-08 03:21:48', 'ca-phe-phin-den-nong'),
(134, 'Cà Phê Phin Đen Đá giữ nguyên vị cà phê đậm đà truyền thống nhưng được kết hợp cùng đá lạnh tạo nên cảm giác sảng khoái và dễ uống hơn. Hương thơm cà phê rang xay cùng hậu vị mạnh mẽ giúp thức uống trở thành lựa chọn quen thuộc của nhiều khách hàng Việt Nam.', 'product-34-1778235863.png', 'active', 28000.00, 0, 'Cà Phê Phin Đen Đá', 12, '2026-05-08 03:24:23', 'ca-phe-phin-den-da'),
(135, 'Cà Phê Phin Nâu Nóng là sự kết hợp giữa cà phê phin truyền thống và sữa đặc ngọt béo, tạo nên hương vị hài hòa và quen thuộc. Vị đắng nhẹ của cà phê hòa cùng độ ngọt dịu giúp thức uống trở nên dễ uống nhưng vẫn đậm đà đặc trưng. Đây là lựa chọn phù hợp cho những ai yêu thích cà phê sữa truyền thống Việt Nam.', 'product-35-1778235914.png', 'active', 25000.00, 0, 'Cà Phê Phin Nâu Nóng', 12, '2026-05-08 03:25:14', 'ca-phe-phin-nau-nong');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_categories`
--

CREATE TABLE `product_categories` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_categories`
--

INSERT INTO `product_categories` (`ID`, `Name`) VALUES
(11, 'Americano'),
(12, 'Phin'),
(13, 'Latte'),
(14, 'Cold Brew'),
(16, 'Espresso'),
(17, 'Frappe'),
(18, 'Milk Tea');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `relied_contacts`
--

CREATE TABLE `relied_contacts` (
  `Admin_ID` int(11) DEFAULT NULL,
  `Contact_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `relied_contacts`
--

INSERT INTO `relied_contacts` (`Admin_ID`, `Contact_ID`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `relied_infor`
--

CREATE TABLE `relied_infor` (
  `Admin_ID` int(11) NOT NULL,
  `reply_content` varchar(255) NOT NULL,
  `reply_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `relied_infor`
--

INSERT INTO `relied_infor` (`Admin_ID`, `reply_content`, `reply_at`) VALUES
(1, 'Đã gọi điện tư vấn và gửi mail quy trình trả góp cho khách.', '2026-05-10 01:23:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `status` enum('active','banned','pending','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`ID`, `first_name`, `last_name`, `email`, `password`, `phone`, `gender`, `birth_date`, `address`, `role`, `status`, `created_at`, `updated_at`, `image`) VALUES
(1, 'Minh', 'Haha', 'admin@gmail.com', 'admin', '0901111222', '', '0000-00-00', '123 Quận 1, TP.HCM', 'admin', 'active', '2026-05-10 01:23:22', '2026-05-10 03:41:01', 'avatar_1_1778384461.jpg'),
(2, 'Hoàng', 'Khách', 'customer1@gmail.com', 'customer', '0903333444', '', '0000-00-00', '456 Quận 7, TP.HCM', 'customer', 'active', '2026-05-10 01:23:22', '2026-05-10 03:41:19', 'avatar_2_1778384479.jpg');

--
-- Bẫy `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.role = 'admin' THEN
        INSERT INTO ADMIN (ID) VALUES (NEW.ID);
    ELSE
        INSERT INTO CUSTOMER (ID, loyalty_point) VALUES (NEW.ID, 0);
        INSERT INTO CARTS (Customer_ID) VALUES (NEW.ID);
    END IF;
END
$$
DELIMITER ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `about_sections`
--
ALTER TABLE `about_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_key` (`section_key`);

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`Cart_ID`,`Product_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `News_ID` (`News_ID`),
  ADD KEY `parent_comment_id` (`parent_comment_id`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`ID`);

--
-- Chỉ mục cho bảng `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`ID`);

--
-- Chỉ mục cho bảng `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `made_contacts`
--
ALTER TABLE `made_contacts`
  ADD PRIMARY KEY (`Contact_ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Chỉ mục cho bảng `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `Admin_ID` (`Admin_ID`),
  ADD KEY `N_Cate_ID` (`N_Cate_ID`);

--
-- Chỉ mục cho bảng `news_categories`
--
ALTER TABLE `news_categories`
  ADD PRIMARY KEY (`ID`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`Order_ID`,`Product_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `P_Cate_ID` (`P_Cate_ID`);

--
-- Chỉ mục cho bảng `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`ID`);

--
-- Chỉ mục cho bảng `relied_contacts`
--
ALTER TABLE `relied_contacts`
  ADD PRIMARY KEY (`Contact_ID`),
  ADD KEY `Admin_ID` (`Admin_ID`);

--
-- Chỉ mục cho bảng `relied_infor`
--
ALTER TABLE `relied_infor`
  ADD PRIMARY KEY (`Admin_ID`,`reply_content`,`reply_at`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `about_sections`
--
ALTER TABLE `about_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `news_categories`
--
ALTER TABLE `news_categories`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT cho bảng `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`ID`);

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`Cart_ID`) REFERENCES `carts` (`ID`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `products` (`ID`);

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`News_ID`) REFERENCES `news` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`ID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `made_contacts`
--
ALTER TABLE `made_contacts`
  ADD CONSTRAINT `made_contacts_ibfk_1` FOREIGN KEY (`Contact_ID`) REFERENCES `contacts` (`ID`),
  ADD CONSTRAINT `made_contacts_ibfk_2` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`ID`);

--
-- Các ràng buộc cho bảng `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`ID`),
  ADD CONSTRAINT `news_ibfk_2` FOREIGN KEY (`N_Cate_ID`) REFERENCES `news_categories` (`ID`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`ID`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`ID`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `products` (`ID`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`P_Cate_ID`) REFERENCES `product_categories` (`ID`);

--
-- Các ràng buộc cho bảng `relied_contacts`
--
ALTER TABLE `relied_contacts`
  ADD CONSTRAINT `relied_contacts_ibfk_1` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`ID`),
  ADD CONSTRAINT `relied_contacts_ibfk_2` FOREIGN KEY (`Contact_ID`) REFERENCES `contacts` (`ID`);

--
-- Các ràng buộc cho bảng `relied_infor`
--
ALTER TABLE `relied_infor`
  ADD CONSTRAINT `relied_infor_ibfk_1` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

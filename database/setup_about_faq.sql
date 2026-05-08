-- =============================================
-- SQL Script: About Sections & FAQs
-- Database: shop_db
-- =============================================

USE shop_db;

-- 1. Bảng about_sections: lưu nội dung trang Giới thiệu
CREATE TABLE IF NOT EXISTS about_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(255) DEFAULT '',
    content TEXT,
    image_url VARCHAR(500) DEFAULT '',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng faqs: lưu câu hỏi/đáp
DROP TABLE IF EXISTS faqs;
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Seed about_sections
INSERT INTO about_sections (section_key, title, content, image_url, sort_order) VALUES
('hero', 'NƠI BẮT ĐẦU NHỮNG NGÀY HIỆU QUẢ', 'Một không gian yên tĩnh, thoải mái cùng ly cà phê chất lượng giúp bạn khởi đầu ngày mới với sự tập trung và năng lượng, để học tập và làm việc hiệu quả hơn.', '', 1),
('origin', 'Nguồn gốc', 'UniPhin Coffee bắt đầu từ một ý tưởng rất đơn giản – tạo ra một không gian dành riêng cho sinh viên, nơi ai cũng có thể tìm thấy sự yên tĩnh giữa nhịp sống bận rộn. Xuất phát từ những trải nghiệm học tập và làm việc chưa trọn vẹn, chúng mình mong muốn xây dựng một \"góc nhỏ\" đủ thoải mái để mỗi người có thể tập trung và phát triển bản thân.\r\n\r\nTừ những bước đầu đó, UniPhin Coffee ra đời với tinh thần gần gũi, giản dị và luôn hướng đến cộng đồng sinh viên.', '', 2),
('mission', 'Sứ mệnh', 'UniPhin Coffee hướng đến việc mang lại những ly cà phê chất lượng với mức giá phù hợp cho sinh viên, đi cùng với đó là một không gian học tập và làm việc hiệu quả.\r\n\r\nChúng mình mong muốn tạo ra một môi trường yên tĩnh, thoải mái, nơi bạn có thể tập trung, hoàn thành công việc và phát triển bản thân mỗi ngày. UniPhin không chỉ phục vụ đồ uống, mà còn đồng hành cùng bạn trong hành trình học tập và làm việc.', '', 3),
('quality', 'Từ hạt cà phê ngon đến ly cà phê trọn vị', 'Chúng mình lựa chọn nguồn cà phê từ những vùng trồng tại Việt Nam, nơi điều kiện tự nhiên và sự chăm sóc của người nông dân tạo nên những hạt cà phê chất lượng. Từng hạt được rang xay cẩn thận, giữ lại hương vị tự nhiên – đậm vừa đủ, dễ uống và phù hợp với nhiều người, đặc biệt là sinh viên.\r\n\r\nVới UniPhin, giá trị không chỉ nằm ở hương vị, mà còn ở cảm giác bạn nhận được khi thưởng thức. Đó có thể là một chút tỉnh táo để tiếp tục công việc, hay đơn giản là một khoảnh khắc dễ chịu giữa ngày dài.', '', 4);

-- 4. Seed faqs
INSERT INTO faqs (question, answer, sort_order, is_active) VALUES
('Thời gian giao hàng là bao lâu?', 'Chúng tôi hỗ trợ giao hàng nhanh trong ngày đối với khu vực nội thành (thường từ 30 phút – 2 giờ tùy khu vực).', 1, 1),
('Làm thế nào để có thể đặt hàng trên website?', 'Bạn chỉ cần chọn sản phẩm, thêm vào giỏ hàng và tiến hành thanh toán. Hệ thống sẽ xác nhận đơn hàng qua email.', 2, 1),
('Shop có hỗ trợ trả góp không?', 'Có, shop hỗ trợ trả góp 0% qua thẻ tín dụng và công ty tài chính.', 3, 1),
('Chính sách đổi trả như thế nào?', 'Sản phẩm được đổi trả trong vòng 7 ngày nếu còn nguyên tem mác và chưa qua sử dụng.', 4, 1),
('Có chương trình khuyến mãi cho sinh viên không?', 'Có, chúng tôi thường xuyên có chương trình giảm giá dành riêng cho sinh viên khi xuất trình thẻ sinh viên.', 5, 1),
('Quán mở cửa từ mấy giờ?', 'Quán mở cửa từ 7:00 sáng đến 22:00 tối hàng ngày, kể cả cuối tuần và ngày lễ.', 6, 1);

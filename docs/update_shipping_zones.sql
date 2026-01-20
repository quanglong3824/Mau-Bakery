-- Tạo bảng shipping_zones
CREATE TABLE IF NOT EXISTS `shipping_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Tên Quận/Huyện',
  `fee` decimal(10,0) NOT NULL DEFAULT 0 COMMENT 'Phí ship',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Hiện, 0=Ẩn',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chèn dữ liệu mẫu (Các quận nội/ngoại thành TP.HCM)
INSERT INTO `shipping_zones` (`name`, `fee`) VALUES
('Quận 1', 15000),
('Quận 3', 15000),
('Quận 4', 15000),
('Quận 5', 15000),
('Quận 10', 15000),
('Quận Bình Thạnh', 15000),
('Quận Phú Nhuận', 15000),
('Quận 6', 30000),
('Quận 7', 30000),
('Quận 8', 30000),
('Quận 11', 30000),
('Quận Tân Bình', 30000),
('Quận Gò Vấp', 30000),
('Quận Tân Phú', 30000),
('Quận 12', 50000),
('Quận Bình Tân', 50000),
('TP. Thủ Đức', 50000),
('Huyện Bình Chánh', 60000),
('Huyện Hóc Môn', 60000),
('Huyện Nhà Bè', 60000),
('Huyện Củ Chi', 70000),
('Huyện Cần Giờ', 100000);

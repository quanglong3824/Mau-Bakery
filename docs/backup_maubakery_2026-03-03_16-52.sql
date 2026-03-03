-- Database Backup: Mâu Bakery
-- Date: 2026-03-03 16:52:25
-- Exported by: admin

CREATE DATABASE IF NOT EXISTS `MauBakery` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `MauBakery`;

SET FOREIGN_KEY_CHECKS=0;



CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `is_active`) VALUES ('1', 'Bánh Sinh Nhật', 'banh-sinh-nhat', '', '1');
INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `is_active`) VALUES ('2', 'Bánh Đặt Tiệc', 'banh-dat-tiec', '', '1');
INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `is_active`) VALUES ('3', 'Bánh Làm Quà Tặng', 'banh-lam-qua-tang', '', '1');
INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `is_active`) VALUES ('4', 'Bánh Kem Trái Cây', 'banh-kem-trai-cay', '', '1');
INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `is_active`) VALUES ('5', 'Sản Phẩm Mới', 'san-pham-moi', NULL, '1');
INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `is_active`) VALUES ('6', 'Bánh Sô-cô-la', 'banh-socola', NULL, '1');
INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `is_active`) VALUES ('7', 'Bánh Tạo Hình', 'banh-tao-hinh', NULL, '1');
INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `is_active`) VALUES ('8', 'Bánh Matcha', 'banh-matcha', NULL, '1');
INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `is_active`) VALUES ('9', 'Tiramisu', 'tiramisu', NULL, '1');


CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `sender_role` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `chat_messages` (`id`, `room_id`, `sender_role`, `message`, `created_at`) VALUES ('1', '1', 'user', 'này sài đc chưa vvv', '2026-03-03 22:26:08');
INSERT INTO `chat_messages` (`id`, `room_id`, `sender_role`, `message`, `created_at`) VALUES ('2', '1', 'admin', 'được rồi nha =))', '2026-03-03 22:26:36');
INSERT INTO `chat_messages` (`id`, `room_id`, `sender_role`, `message`, `created_at`) VALUES ('3', '2', 'user', 'Chafo sho', '2026-03-03 22:33:01');
INSERT INTO `chat_messages` (`id`, `room_id`, `sender_role`, `message`, `created_at`) VALUES ('4', '2', 'admin', 'Daj Maau nghe', '2026-03-03 22:33:30');


CREATE TABLE `chat_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT 'Khách hàng',
  `last_message` text DEFAULT NULL,
  `is_read_by_admin` tinyint(4) DEFAULT 0,
  `is_read_by_user` tinyint(4) DEFAULT 1,
  `status` enum('open','closed') DEFAULT 'open',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `chat_rooms` (`id`, `user_id`, `session_id`, `customer_name`, `last_message`, `is_read_by_admin`, `is_read_by_user`, `status`, `updated_at`, `created_at`) VALUES ('1', NULL, '7pqsb86g0u0n9jbukvkd04o86o', 'Khách vãng lai', 'được rồi nha =))', '1', '0', 'open', '2026-03-03 22:26:36', '2026-03-03 22:26:08');
INSERT INTO `chat_rooms` (`id`, `user_id`, `session_id`, `customer_name`, `last_message`, `is_read_by_admin`, `is_read_by_user`, `status`, `updated_at`, `created_at`) VALUES ('2', NULL, 'evg60ksnn0v8snj7aiqd4pd3a9', 'Khách vãng lai', 'Daj Maau nghe', '1', '1', 'open', '2026-03-03 22:33:32', '2026-03-03 22:33:01');


CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `discount_type` enum('percent','fixed') DEFAULT 'fixed',
  `min_order` decimal(10,2) DEFAULT 0.00,
  `expiry_date` datetime NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `coupons` (`id`, `code`, `discount_value`, `discount_type`, `min_order`, `expiry_date`, `quantity`, `is_active`, `created_at`) VALUES ('1', 'WELCOME20', '20.00', 'percent', '0.00', '2026-12-31 23:59:59', '100', '1', '2026-03-03 22:03:27');
INSERT INTO `coupons` (`id`, `code`, `discount_value`, `discount_type`, `min_order`, `expiry_date`, `quantity`, `is_active`, `created_at`) VALUES ('2', 'FREESHIP', '30000.00', 'fixed', '0.00', '2026-06-30 23:59:59', '49', '1', '2026-03-03 22:03:27');


CREATE TABLE `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_favorites_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_favorites_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES ('1', '7', '1', '2026-01-22 14:55:24');


CREATE TABLE `featured_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `featured_tags` (`id`, `name`, `url`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES ('1', 'Mùa Dâu', 'index.php?page=product_detail&id=2', '', '0', '1', '2026-01-22 14:09:12', '2026-01-22 14:10:56');
INSERT INTO `featured_tags` (`id`, `name`, `url`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES ('2', 'Mùa trái cây', 'index.php?page=menu&ids=1,2,9', '', '1', '1', '2026-01-22 14:13:08', '2026-01-22 14:13:08');


CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(150) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `fk_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('1', '1', '1', 'Bánh Kem Chanh Vàng', '16cm (Nhỏ)', '350000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('2', '2', '1', 'Bánh Kem Chanh Vàng', 'Standard', '480000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('3', '3', '2', 'Bánh Kem Dâu', 'Standard', '250000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('4', '4', '20', 'Bánh Tiramisu Hình Mèo', 'Standard', '230000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('5', '5', '17', 'Bánh Sinh Nhật Socola Chảy', '20cm (Vừa)', '460000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('6', '6', '2', 'Bánh Kem Dâu', '20cm (Vừa)', '250000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('7', '6', '6', 'Bánh Kem Hình Gấu Hồng', '20cm (Vừa)', '250000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('8', '6', '19', 'Bánh Sinh Nhật Xanh Pastel Dâu Tây', '20cm (Vừa)', '400000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('9', '7', '3', 'Bánh Kem Dâu', '20cm (Vừa)', '380000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('10', '8', '8', 'Bánh Kem Matcha Oreo', '20cm (Vừa)', '200000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('11', '9', '3', 'Bánh Kem Dâu', '20cm (Vừa)', '380000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('12', '9', '17', 'Bánh Sinh Nhật Socola Chảy', '20cm (Vừa)', '460000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('13', '9', '18', 'Bánh Sinh Nhật Trái Cây Nhiệt Đới', '20cm (Vừa)', '340000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('14', '10', '2', 'Bánh Kem Dâu', '20cm (Vừa)', '250000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('15', '10', '17', 'Bánh Sinh Nhật Socola Chảy', '20cm (Vừa)', '460000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('16', '10', '20', 'Bánh Tiramisu Hình Mèo', '20cm (Vừa)', '230000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('17', '11', '6', 'Bánh Kem Hình Gấu Hồng', '20cm (Vừa)', '250000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('18', '12', '2', 'Bánh Kem Dâu', '20cm (Vừa)', '250000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('19', '12', '9', 'Bánh Kem Nho Xanh', '20cm (Vừa)', '190000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('20', '12', '15', 'Bánh Kem Xanh Nhân Socola', '20cm (Vừa)', '420000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('21', '13', '12', 'Bánh Kem Trà Xanh Phủ Socola', '20cm (Vừa)', '430000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('22', '13', '19', 'Bánh Sinh Nhật Xanh Pastel Dâu Tây', '20cm (Vừa)', '400000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('23', '14', '2', 'Bánh Kem Dâu', '20cm (Vừa)', '250000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('24', '14', '7', 'Bánh Kem Hình Núi Băng', '20cm (Vừa)', '170000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('25', '15', '7', 'Bánh Kem Hình Núi Băng', '20cm (Vừa)', '170000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('26', '15', '14', 'Bánh Kem Xanh Hình Con Gấu', '20cm (Vừa)', '400000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('27', '16', '6', 'Bánh Kem Hình Gấu Hồng', '20cm (Vừa)', '250000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('28', '16', '20', 'Bánh Tiramisu Hình Mèo', '20cm (Vừa)', '230000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('29', '17', '2', 'Bánh Kem Dâu', '20cm (Vừa)', '250000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('30', '17', '9', 'Bánh Kem Nho Xanh', '20cm (Vừa)', '190000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('31', '18', '16', 'Bánh Mochi Dẻo Socola', '20cm (Vừa)', '400000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('32', '19', '7', 'Bánh Kem Hình Núi Băng', '20cm (Vừa)', '170000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('33', '20', '10', 'Bánh Kem Phủ Bánh Quy Socola', '20cm (Vừa)', '490000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('34', '21', '9', 'Bánh Kem Nho Xanh', '20cm (Vừa)', '190000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('35', '21', '18', 'Bánh Sinh Nhật Trái Cây Nhiệt Đới', '20cm (Vừa)', '340000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('36', '22', '6', 'Bánh Kem Hình Gấu Hồng', '20cm (Vừa)', '250000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('37', '22', '10', 'Bánh Kem Phủ Bánh Quy Socola', '20cm (Vừa)', '490000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('38', '22', '15', 'Bánh Kem Xanh Nhân Socola', '20cm (Vừa)', '420000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('39', '23', '14', 'Bánh Kem Xanh Hình Con Gấu', '20cm (Vừa)', '400000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('40', '23', '20', 'Bánh Tiramisu Hình Mèo', '20cm (Vừa)', '230000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('41', '24', '12', 'Bánh Kem Trà Xanh Phủ Socola', '20cm (Vừa)', '430000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('42', '24', '20', 'Bánh Tiramisu Hình Mèo', '20cm (Vừa)', '230000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('43', '25', '5', 'Bánh Kem Gấu Trắng', '20cm (Vừa)', '420000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('44', '26', '14', 'Bánh Kem Xanh Hình Con Gấu', '20cm (Vừa)', '400000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('45', '26', '17', 'Bánh Sinh Nhật Socola Chảy', '20cm (Vừa)', '460000.00', '1');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('46', '26', '18', 'Bánh Sinh Nhật Trái Cây Nhiệt Đới', '20cm (Vừa)', '340000.00', '2');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `price`, `quantity`) VALUES ('47', '27', '2', 'Bánh Kem Dâu', 'Standard', '250000.00', '1');


CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `order_code` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('cod','banking','momo') DEFAULT 'cod',
  `status` enum('pending','confirmed','shipping','completed','failed','cancelled') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid',
  `note` text DEFAULT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `recipient_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('1', '7', 'ORD-20260120-2163', '715000.00', '15000.00', '0.00', 'cod', 'completed', 'unpaid', 'á', 'Quang Long', '0987654321', 'sdfgh, Quận 4, TP. Hồ Chí Minh', '1', '2026-01-20 18:09:29');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('2', '8', 'ORD-20260303-3675', '495000.00', '15000.00', '0.00', 'cod', 'completed', 'unpaid', '', 'phuong thao', '0777650029', 'abc, Quận 4, TP. Hồ Chí Minh', '1', '2026-03-03 20:37:15');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('3', NULL, 'ORD-20260303-3296', '265000.00', '15000.00', '0.00', 'cod', 'completed', 'unpaid', '', 'phuongthao', '098823993r', 'abc, Quận 5, TP. Hồ Chí Minh', '1', '2026-03-03 20:38:34');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('4', '1', 'ORD-20260303-2082', '230000.00', '30000.00', '30000.00', 'cod', 'completed', 'unpaid', '', 'Admin System', '0909000111', 'abc, Quận 7, TP. Hồ Chí Minh', '1', '2026-03-03 21:40:46');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('5', '8', 'ORD-20260303-1000', '935000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Trần Thị Kim Anh', '0912345678', '45 Lê Thánh Tôn, Quận 1', '1', '2026-03-03 10:15:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('6', '13', 'ORD-20260303-1001', '1565000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Lê Hoàng Nam', '0988776655', '120 Trần Não, TP. Thủ Đức', '1', '2026-03-03 14:20:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('7', '12', 'ORD-20260302-1002', '395000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Nguyễn Minh Đức', '0345678901', '789 Võ Văn Tần, Quận 3', '1', '2026-03-02 09:30:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('8', '13', 'ORD-20260301-1003', '215000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Vũ Thị Mai', '0933221100', '22/5 Phan Xích Long, Phú Nhuận', '1', '2026-03-01 18:45:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('9', '10', 'ORD-20260301-1004', '1575000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Phạm Thu Thảo', '0901234455', 'Sky Garden 2, Quận 7', '1', '2026-03-01 20:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('10', '9', 'ORD-20260228-1005', '1645000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Trần Thị Kim Anh', '0912345678', '45 Lê Thánh Tôn, Quận 1', '1', '2026-02-28 11:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('11', '12', 'ORD-20260214-1006', '265000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Lê Hoàng Nam', '0988776655', 'Khách sạn Caravelle, Quận 1', '1', '2026-02-14 08:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('12', '8', 'ORD-20260214-1007', '1485000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Phạm Thu Thảo', '0901234455', 'Lotte Mart Quận 7', '1', '2026-02-14 10:30:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('13', '10', 'ORD-20260214-1008', '845000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Vũ Thị Mai', '0933221100', 'Chung cư Miếu Nổi, Bình Thạnh', '1', '2026-02-14 15:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('14', '12', 'ORD-20260210-1009', '605000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Nguyễn Minh Đức', '0345678901', 'KDC Trung Sơn, Bình Chánh', '1', '2026-02-10 16:20:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('15', '10', 'ORD-20260205-1010', '585000.00', '15000.00', '0.00', 'cod', 'cancelled', 'unpaid', NULL, 'Lê Hoàng Nam', '0988776655', 'Bạch Đằng, Tân Bình', '1', '2026-02-05 12:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('16', '13', 'ORD-20260125-1011', '975000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Vũ Thị Mai', '0933221100', '22/5 Phan Xích Long, Phú Nhuận', '1', '2026-01-25 09:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('17', '7', 'ORD-20260120-1012', '645000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Trần Thị Kim Anh', '0912345678', 'Landmark 81, Bình Thạnh', '1', '2026-01-20 14:30:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('18', '7', 'ORD-20260115-1013', '815000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Phạm Thu Thảo', '0901234455', 'Sky Garden 2, Quận 7', '1', '2026-01-15 11:20:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('19', '7', 'ORD-20260110-1014', '355000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Nguyễn Minh Đức', '0345678901', 'Hẻm 128 Đoàn Văn Bơ, Quận 4', '1', '2026-01-10 17:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('20', '10', 'ORD-20251224-1015', '995000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Lê Hoàng Nam', '0988776655', 'Diamond Plaza, Quận 1', '1', '2025-12-24 19:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('21', '9', 'ORD-20251224-1016', '545000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Vũ Thị Mai', '0933221100', 'Chung cư Carina, Quận 8', '1', '2025-12-24 20:30:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('22', '9', 'ORD-20251220-1017', '1665000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Trần Thị Kim Anh', '0912345678', 'KDC Him Lam, Quận 7', '1', '2025-12-20 10:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('23', '7', 'ORD-20251120-1018', '875000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Nguyễn Minh Đức', '0345678901', 'Đại học Bách Khoa, Quận 10', '1', '2025-11-20 09:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('24', '8', 'ORD-20251115-1019', '1105000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Phạm Thu Thảo', '0901234455', 'Cư xá Đô Thành, Quận 3', '1', '2025-11-15 14:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('25', '12', 'ORD-20251010-1020', '855000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Lê Hoàng Nam', '0988776655', 'TP. Thủ Đức', '1', '2025-10-10 16:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('26', '13', 'ORD-20251005-1021', '1955000.00', '15000.00', '0.00', 'cod', 'completed', 'paid', NULL, 'Vũ Thị Mai', '0933221100', 'Quận 12', '1', '2025-10-05 10:00:00');
INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `payment_method`, `status`, `payment_status`, `note`, `recipient_name`, `recipient_phone`, `shipping_address`, `is_active`, `created_at`) VALUES ('27', NULL, 'ORD-20260303-5277', '280000.00', '30000.00', '0.00', 'cod', 'pending', 'unpaid', '', 'phuong thao', '0987362784r', 'abc, Quận 6, TP. Hồ Chí Minh', '1', '2026-03-03 22:06:21');


CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `image` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_url` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_images_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `size_name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_variants_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `product_variants` (`id`, `product_id`, `size_name`, `price`, `stock_quantity`, `is_active`) VALUES ('1', '1', '16cm (Nhỏ)', '350000.00', '10', '1');
INSERT INTO `product_variants` (`id`, `product_id`, `size_name`, `price`, `stock_quantity`, `is_active`) VALUES ('2', '1', '20cm (Vừa)', '450000.00', '5', '1');
INSERT INTO `product_variants` (`id`, `product_id`, `size_name`, `price`, `stock_quantity`, `is_active`) VALUES ('3', '1', '24cm (Lớn)', '600000.00', '2', '1');
INSERT INTO `product_variants` (`id`, `product_id`, `size_name`, `price`, `stock_quantity`, `is_active`) VALUES ('4', '2', '16cm (Nhỏ)', '400000.00', '8', '1');
INSERT INTO `product_variants` (`id`, `product_id`, `size_name`, `price`, `stock_quantity`, `is_active`) VALUES ('5', '2', '20cm (Vừa)', '550000.00', '4', '1');
INSERT INTO `product_variants` (`id`, `product_id`, `size_name`, `price`, `stock_quantity`, `is_active`) VALUES ('6', '5', 'Hộp 500ml', '150000.00', '20', '1');
INSERT INTO `product_variants` (`id`, `product_id`, `size_name`, `price`, `stock_quantity`, `is_active`) VALUES ('7', '9', 'Cái', '35000.00', '50', '1');
INSERT INTO `product_variants` (`id`, `product_id`, `size_name`, `price`, `stock_quantity`, `is_active`) VALUES ('8', '13', 'Hộp 6 cái', '180000.00', '10', '1');


CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `image` text DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_products_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('1', '4', 'Bánh Kem Chanh Vàng', 'banh-kem-chanh-vang', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '480000.00', 'uploads/banh-kem-chanh-vang.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('2', '4', 'Bánh Kem Dâu', 'banh-kem-dau-tay', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '250000.00', 'uploads/banh-kem-dau-tay.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('3', '5', 'Bánh Kem Dâu', 'banh-kem-dau', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '380000.00', 'uploads/banh-kem-dau.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('4', '6', 'Bánh Kem Gấu Socola', 'banh-kem-gau-socola', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '160000.00', 'uploads/banh-kem-gau-socola.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('5', '7', 'Bánh Kem Gấu Trắng', 'banh-kem-gau-trang', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '420000.00', 'uploads/banh-kem-gau-trang.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('6', '7', 'Bánh Kem Hình Gấu Hồng', 'banh-kem-hinh-gau-hong', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '250000.00', 'uploads/banh-kem-hinh-gau-hong.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('7', '7', 'Bánh Kem Hình Núi Băng', 'banh-kem-hinh-nui-bang', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '170000.00', 'uploads/banh-kem-hinh-nui-bang.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('8', '8', 'Bánh Kem Matcha Oreo', 'banh-kem-matcha-oreo', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '200000.00', 'uploads/banh-kem-matcha-oreo.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('9', '4', 'Bánh Kem Nho Xanh', 'banh-kem-nho-xanh', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '190000.00', 'uploads/banh-kem-nho-xanh.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('10', '6', 'Bánh Kem Phủ Bánh Quy Socola', 'banh-kem-phu-banh-quy-socola', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '490000.00', 'uploads/banh-kem-phu-banh-quy-socola.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('11', '6', 'Bánh Kem Socola Kèm Bông Lan', 'banh-kem-socola-kem-bong-lan', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '360000.00', 'uploads/banh-kem-socola-kem-bong-lan.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('12', '6', 'Bánh Kem Trà Xanh Phủ Socola', 'banh-kem-tra-xanh-phu-socola', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '430000.00', 'uploads/banh-kem-tra-xanh-phu-socola.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('13', '4', 'Bánh Kem Trắng Dâu Tây', 'banh-kem-trang-dau-tay', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '450000.00', 'uploads/banh-kem-trang-dau-tay.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('14', '7', 'Bánh Kem Xanh Hình Con Gấu', 'banh-kem-xanh-hinh-con-gau', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '400000.00', 'uploads/banh-kem-xanh-hinh-con-gau.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('15', '6', 'Bánh Kem Xanh Nhân Socola', 'banh-kem-xanh-nhan-socola', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '420000.00', 'uploads/banh-kem-xanh-nhan-socola.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('16', '6', 'Bánh Mochi Dẻo Socola', 'banh-mochi-deo-socola', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '400000.00', 'uploads/banh-mochi-deo-socola.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('17', '1', 'Bánh Sinh Nhật Socola Chảy', 'banh-sinh-nhat-socola-chay', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '460000.00', 'uploads/banh-sinh-nhat-socola-chay.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('18', '1', 'Bánh Sinh Nhật Trái Cây Nhiệt Đới', 'banh-sinh-nhat-trai-cay-nhiet-doi', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '340000.00', 'uploads/banh-sinh-nhat-trai-cay-nhiet-doi.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('19', '1', 'Bánh Sinh Nhật Xanh Pastel Dâu Tây', 'banh-sinh-nhat-xanh-pastel-dau-tay', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '400000.00', 'uploads/banh-sinh-nhat-xanh-pastel-dau-tay.jpg', '0', '0', '1', '2026-01-09 16:47:30');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `base_price`, `image`, `views`, `is_featured`, `is_active`, `created_at`) VALUES ('20', '9', 'Bánh Tiramisu Hình Mèo', 'banh-tiramisu-hinh-meo', 'Bánh tươi mỗi ngày, nguyên liệu cao cấp.', '230000.00', 'uploads/banh-tiramisu-hinh-meo.jpg', '0', '0', '1', '2026-01-09 16:47:30');


CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT 5,
  `comment` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_reviews_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `shipping_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Tên Quận/Huyện',
  `fee` decimal(10,0) NOT NULL DEFAULT 0 COMMENT 'Phí ship',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Hiện, 0=Ẩn',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('1', 'Quận 1', '15000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('2', 'Quận 3', '15000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('3', 'Quận 4', '15000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('4', 'Quận 5', '15000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('5', 'Quận 10', '15000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('6', 'Quận Bình Thạnh', '15000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('7', 'Quận Phú Nhuận', '15000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('8', 'Quận 6', '30000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('9', 'Quận 7', '30000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('10', 'Quận 8', '30000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('11', 'Quận 11', '30000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('12', 'Quận Tân Bình', '30000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('13', 'Quận Gò Vấp', '30000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('14', 'Quận Tân Phú', '30000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('15', 'Quận 12', '50000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('16', 'Quận Bình Tân', '50000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('17', 'TP. Thủ Đức', '50000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('18', 'Huyện Bình Chánh', '60000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('19', 'Huyện Hóc Môn', '60000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('20', 'Huyện Nhà Bè', '60000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('21', 'Huyện Củ Chi', '70000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');
INSERT INTO `shipping_zones` (`id`, `name`, `fee`, `is_active`, `created_at`, `updated_at`) VALUES ('22', 'Huyện Cần Giờ', '100000', '1', '2026-01-20 18:06:57', '2026-01-20 18:06:57');


CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_user_addresses_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `is_default`, `is_active`) VALUES ('1', '2', 'Quang Long', '0909000222', '123 Nguyễn Huệ, Quận 1, TP.HCM', '1', '1');
INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `is_default`, `is_active`) VALUES ('2', '2', 'Quang Long (Cty)', '0909000222', '456 Lê Duẩn, Quận 1, TP.HCM', '0', '1');
INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `is_default`, `is_active`) VALUES ('3', '3', 'Thu Hà', '0909000333', '789 Võ Văn Tần, Quận 3, TP.HCM', '1', '1');
INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `is_default`, `is_active`) VALUES ('4', '6', 'Long Meo Meo', '0987654321', '123 Biên Hoà Đồng Nai', '1', '1');
INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `is_default`, `is_active`) VALUES ('5', '7', 'sdcs', '12345679000', 'sdcdsc', '1', '1');


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `points` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `avatar`, `role`, `points`, `is_active`, `created_at`) VALUES ('1', 'admin', '$2y$10$7Uq7qtWO1yu3j6SUUkXUEOHeXRRBEiNDpWJJsESZ/8qQft79BS.Ba', 'admin@MauBakery.com', 'Admin System', '0909000111', NULL, 'admin', '0', '1', '2026-01-07 21:03:34');
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `avatar`, `role`, `points`, `is_active`, `created_at`) VALUES ('7', 'longmeomeo', '$2y$10$hPIJ1XzQBxTz7DAGBJZuXeZfm6zoHNPqlns7TlUMFjn8mD5ItP9Ei', 'long.lequang308@gmail.com', 'Quang Long', '0987654321', NULL, 'user', '0', '1', '2026-01-20 18:08:18');
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `avatar`, `role`, `points`, `is_active`, `created_at`) VALUES ('8', 'phuong thao', '$2y$10$Q9gHPS2SDUo6HF1ofo2fJOltd4R1feHkPIiDlQhNzqRgMVVtqlGXq', 'hikarichoan@gmail.com', 'phuong thao', '0777650029', NULL, 'user', '0', '1', '2026-03-03 20:36:30');
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `avatar`, `role`, `points`, `is_active`, `created_at`) VALUES ('9', 'kimanh_trituan', '$2y$10$iEdzHVN82nggjHc2xjXkY.dmBNMLTn.eIdoYKoySuUkCzMLrIH0be', 'kimanh.tran@gmail.com', 'Trần Thị Kim Anh', '0912345678', NULL, 'user', '0', '1', '2026-03-03 21:53:08');
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `avatar`, `role`, `points`, `is_active`, `created_at`) VALUES ('10', 'hoangnam_le', '$2y$10$9O32/DH8lJsnaKEdzl92uepnrFMk4djNIprBgGnUcLZNJFgLEDMF.', 'nam.lehoang@yahoo.com', 'Lê Hoàng Nam', '0988776655', NULL, 'user', '0', '1', '2026-03-03 21:53:08');
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `avatar`, `role`, `points`, `is_active`, `created_at`) VALUES ('11', 'thuthao_pham', '$2y$10$mqG1rcQsRhdHL/4.ndTRHO9Y7MDyFcRQvJumUHvzxE8llxl7K1Thy', 'thaopham.90@gmail.com', 'Phạm Thu Thảo', '0901234455', NULL, 'user', '0', '1', '2026-03-03 21:53:08');
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `avatar`, `role`, `points`, `is_active`, `created_at`) VALUES ('12', 'minhduc_nguyen', '$2y$10$4klgdUs5Ac8/aufvAm1qTOWEVMPY4ywMXbfAf5tAyavf2EUfQJZoO', 'duc.minh.nguyen@outlook.com', 'Nguyễn Minh Đức', '0345678901', NULL, 'user', '0', '1', '2026-03-03 21:53:08');
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `avatar`, `role`, `points`, `is_active`, `created_at`) VALUES ('13', 'maivu_cake', '$2y$10$GKPmbSi5ldxf3j78aHfTDeJjwGZ12aB.5iN8DvGmhgkvW.ad815oS', 'maivu.hcm@gmail.com', 'Vũ Thị Mai', '0933221100', NULL, 'user', '0', '1', '2026-03-03 21:53:08');

SET FOREIGN_KEY_CHECKS=1;

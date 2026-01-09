-- Database Backup: Mâu Bakery
-- Date: 2026-01-09 11:00:37
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `coupons` (`id`, `code`, `discount_value`, `discount_type`, `min_order`, `expiry_date`, `quantity`, `is_active`) VALUES ('1', 'WELCOME20', '20.00', 'percent', '0.00', '2026-12-31 23:59:59', '100', '1');
INSERT INTO `coupons` (`id`, `code`, `discount_value`, `discount_type`, `min_order`, `expiry_date`, `quantity`, `is_active`) VALUES ('2', 'FREESHIP', '30000.00', 'fixed', '0.00', '2026-06-30 23:59:59', '50', '1');


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `is_default`, `is_active`) VALUES ('1', '2', 'Quang Long', '0909000222', '123 Nguyễn Huệ, Quận 1, TP.HCM', '1', '1');
INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `is_default`, `is_active`) VALUES ('2', '2', 'Quang Long (Cty)', '0909000222', '456 Lê Duẩn, Quận 1, TP.HCM', '0', '1');
INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `is_default`, `is_active`) VALUES ('3', '3', 'Thu Hà', '0909000333', '789 Võ Văn Tần, Quận 3, TP.HCM', '1', '1');
INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `is_default`, `is_active`) VALUES ('4', '6', 'Long Meo Meo', '0987654321', '123 Biên Hoà Đồng Nai', '1', '1');


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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `avatar`, `role`, `points`, `is_active`, `created_at`) VALUES ('1', 'admin', '$2y$10$7Uq7qtWO1yu3j6SUUkXUEOHeXRRBEiNDpWJJsESZ/8qQft79BS.Ba', 'admin@MauBakery.com', 'Admin System', '0909000111', NULL, 'admin', '0', '1', '2026-01-07 21:03:34');

SET FOREIGN_KEY_CHECKS=1;

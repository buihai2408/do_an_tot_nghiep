-- ============================================================
-- Coffee Shop - Cơ sở dữ liệu MySQL
-- Dự án: Đồ án tốt nghiệp - Hệ thống quản lý quán cà phê
-- Stack: Laravel 12 + Vue 3 (Inertia.js)
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS `coffee_shop`;
CREATE DATABASE `coffee_shop`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `coffee_shop`;

-- ============================================================
-- 1. BẢNG HỆ THỐNG LARAVEL
-- ============================================================

-- 1.1 Người dùng
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL COMMENT 'Không bắt buộc, dùng SĐT để đăng nhập',
    `phone` VARCHAR(20) NOT NULL COMMENT 'Số điện thoại VN, dùng để đăng nhập',
    `role` ENUM('customer', 'admin', 'staff') NOT NULL DEFAULT 'customer',
    `loyalty_points` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Điểm thưởng hiện có (có thể dùng)',
    `total_points_earned` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Tổng điểm đã tích lũy (xác định hạng)',
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.2 Token đặt lại mật khẩu
CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.3 Phiên đăng nhập
CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.4 Cache
CREATE TABLE `cache` (
    `key` VARCHAR(255) NOT NULL,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) NOT NULL,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.5 Hàng đợi (Queue)
CREATE TABLE `jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED DEFAULT NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
    `id` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT DEFAULT NULL,
    `cancelled_at` INT DEFAULT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(255) NOT NULL,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. QUẢN LÝ SẢN PHẨM
-- ============================================================

-- 2.1 Danh mục sản phẩm
CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Tên danh mục (VD: Cà phê, Trà sữa)',
    `slug` VARCHAR(255) NOT NULL COMMENT 'Đường dẫn URL thân thiện',
    `sort_order` INT NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_slug_unique` (`slug`),
    KEY `categories_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.2 Sản phẩm
CREATE TABLE `products` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL COMMENT 'Tên sản phẩm',
    `slug` VARCHAR(255) NOT NULL COMMENT 'Đường dẫn URL thân thiện',
    `description` TEXT DEFAULT NULL,
    `base_price` DECIMAL(12,0) NOT NULL COMMENT 'Giá cơ bản (VNĐ)',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Sản phẩm nổi bật trên trang chủ',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft delete',
    PRIMARY KEY (`id`),
    UNIQUE KEY `products_slug_unique` (`slug`),
    KEY `products_active_featured_index` (`is_active`, `is_featured`),
    CONSTRAINT `products_category_id_foreign`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.3 Hình ảnh sản phẩm (hỗ trợ nhiều ảnh/sản phẩm)
CREATE TABLE `product_images` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `path` VARCHAR(255) NOT NULL COMMENT 'Đường dẫn file ảnh trong storage',
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Ảnh chính hiển thị trước',
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `product_images_product_id_index` (`product_id`),
    CONSTRAINT `product_images_product_id_foreign`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.4 Kích thước đồ uống (S, M, L)
CREATE TABLE `sizes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Mã kích thước (S, M, L)',
    `label` VARCHAR(255) NOT NULL COMMENT 'Tên hiển thị (Nhỏ, Vừa, Lớn)',
    `sort_order` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.5 Sản phẩm - Kích thước (pivot, mỗi size có giá riêng)
CREATE TABLE `product_size` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `size_id` BIGINT UNSIGNED NOT NULL,
    `price` DECIMAL(12,0) NOT NULL COMMENT 'Giá bán theo size (VNĐ)',
    PRIMARY KEY (`id`),
    UNIQUE KEY `product_size_unique` (`product_id`, `size_id`),
    CONSTRAINT `product_size_product_id_foreign`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
    CONSTRAINT `product_size_size_id_foreign`
        FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.6 Topping
CREATE TABLE `toppings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Tên topping (VD: Trân châu, Thạch)',
    `price` DECIMAL(12,0) NOT NULL COMMENT 'Giá topping (VNĐ)',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `toppings_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.7 Sản phẩm - Topping (pivot, topping nào khả dụng cho sản phẩm nào)
CREATE TABLE `product_topping` (
    `product_id` BIGINT UNSIGNED NOT NULL,
    `topping_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`product_id`, `topping_id`),
    CONSTRAINT `product_topping_product_id_foreign`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
    CONSTRAINT `product_topping_topping_id_foreign`
        FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. GIỎ HÀNG
-- ============================================================

-- 3.1 Giỏ hàng (1 user / 1 session = 1 giỏ)
CREATE TABLE `carts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'NULL nếu là khách vãng lai',
    `session_id` VARCHAR(255) DEFAULT NULL COMMENT 'Session ID cho khách vãng lai',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `carts_user_id_unique` (`user_id`),
    KEY `carts_session_id_index` (`session_id`),
    CONSTRAINT `carts_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.2 Sản phẩm trong giỏ hàng
CREATE TABLE `cart_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cart_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `size_id` BIGINT UNSIGNED DEFAULT NULL,
    `ice_level` ENUM('none', 'less', 'normal', 'more') NOT NULL DEFAULT 'normal',
    `sugar_level` ENUM('none', 'less', 'normal', 'more') NOT NULL DEFAULT 'normal',
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(12,0) NOT NULL COMMENT 'Giá tại thời điểm thêm vào giỏ',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `cart_items_cart_id_index` (`cart_id`),
    CONSTRAINT `cart_items_cart_id_foreign`
        FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cart_items_product_id_foreign`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cart_items_size_id_foreign`
        FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.3 Topping trong giỏ hàng (pivot)
CREATE TABLE `cart_item_topping` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cart_item_id` BIGINT UNSIGNED NOT NULL,
    `topping_id` BIGINT UNSIGNED NOT NULL,
    `price` DECIMAL(12,0) NOT NULL COMMENT 'Giá topping tại thời điểm thêm',
    PRIMARY KEY (`id`),
    KEY `cart_item_topping_cart_item_id_index` (`cart_item_id`),
    CONSTRAINT `cart_item_topping_cart_item_id_foreign`
        FOREIGN KEY (`cart_item_id`) REFERENCES `cart_items` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cart_item_topping_topping_id_foreign`
        FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. MÃ GIẢM GIÁ (COUPON)
-- ============================================================

CREATE TABLE `coupons` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(255) NOT NULL COMMENT 'Mã coupon (VD: GIAM20)',
    `name` VARCHAR(255) NOT NULL COMMENT 'Tên/mô tả coupon',
    `type` ENUM('percentage', 'fixed') NOT NULL COMMENT 'Loại: phần trăm hoặc số tiền cố định',
    `value` DECIMAL(12,0) NOT NULL COMMENT 'Giá trị (% hoặc VNĐ tùy type)',
    `min_order_amount` DECIMAL(12,0) NOT NULL DEFAULT 0 COMMENT 'Giá trị đơn hàng tối thiểu',
    `max_discount` DECIMAL(12,0) DEFAULT NULL COMMENT 'Giảm tối đa (cho type percentage)',
    `usage_limit` INT DEFAULT NULL COMMENT 'Số lần sử dụng tối đa (NULL = không giới hạn)',
    `used_count` INT NOT NULL DEFAULT 0 COMMENT 'Số lần đã sử dụng',
    `starts_at` DATETIME DEFAULT NULL COMMENT 'Ngày bắt đầu hiệu lực',
    `expires_at` DATETIME DEFAULT NULL COMMENT 'Ngày hết hạn',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `coupons_code_unique` (`code`),
    KEY `coupons_active_dates_index` (`is_active`, `starts_at`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. ĐỊA CHỈ GIAO HÀNG
-- ============================================================

CREATE TABLE `addresses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `label` VARCHAR(255) DEFAULT NULL COMMENT 'Nhãn (VD: Nhà, Công ty)',
    `recipient_name` VARCHAR(255) NOT NULL COMMENT 'Tên người nhận',
    `phone` VARCHAR(20) NOT NULL,
    `address_line` TEXT NOT NULL COMMENT 'Địa chỉ chi tiết',
    `is_default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Địa chỉ mặc định',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `addresses_user_id_index` (`user_id`),
    CONSTRAINT `addresses_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. ĐƠN HÀNG
-- ============================================================

-- 6.1 Đơn hàng
CREATE TABLE `orders` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'NULL nếu user bị xóa',
    `order_number` VARCHAR(255) NOT NULL COMMENT 'Mã đơn hàng (VD: CF20260316A1B2C3)',
    `status` ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivering', 'completed', 'cancelled')
        NOT NULL DEFAULT 'pending' COMMENT 'Trạng thái đơn hàng',
    `order_type` ENUM('delivery', 'pickup') NOT NULL DEFAULT 'delivery' COMMENT 'Giao hàng hoặc tự đến lấy',
    `subtotal` DECIMAL(12,0) NOT NULL COMMENT 'Tạm tính trước giảm giá',
    `discount_amount` DECIMAL(12,0) NOT NULL DEFAULT 0 COMMENT 'Số tiền giảm từ coupon',
    `shipping_fee` DECIMAL(12,0) NOT NULL DEFAULT 0 COMMENT 'Phí giao hàng',
    `total` DECIMAL(12,0) NOT NULL COMMENT 'Tổng thanh toán',
    `points_earned` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Điểm thưởng nhận được',
    `points_used` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Điểm thưởng đã dùng',
    `points_discount` DECIMAL(12,0) NOT NULL DEFAULT 0 COMMENT 'Số tiền giảm từ điểm thưởng',
    `coupon_id` BIGINT UNSIGNED DEFAULT NULL,
    `payment_method` ENUM('cod', 'bank_transfer') NOT NULL DEFAULT 'cod' COMMENT 'Phương thức thanh toán',
    `payment_status` ENUM('pending', 'paid', 'refunded') NOT NULL DEFAULT 'pending' COMMENT 'Trạng thái thanh toán',
    `customer_name` VARCHAR(255) NOT NULL COMMENT 'Tên người đặt (snapshot)',
    `customer_phone` VARCHAR(20) NOT NULL,
    `customer_email` VARCHAR(255) DEFAULT NULL,
    `shipping_address` TEXT DEFAULT NULL COMMENT 'Địa chỉ giao (NULL nếu pickup)',
    `note` TEXT DEFAULT NULL COMMENT 'Ghi chú của khách',
    `confirmed_at` DATETIME DEFAULT NULL,
    `completed_at` DATETIME DEFAULT NULL,
    `cancelled_at` DATETIME DEFAULT NULL,
    `cancel_reason` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `orders_order_number_unique` (`order_number`),
    KEY `orders_user_id_index` (`user_id`),
    KEY `orders_status_index` (`status`),
    KEY `orders_created_at_index` (`created_at`),
    CONSTRAINT `orders_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `orders_coupon_id_foreign`
        FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.2 Chi tiết đơn hàng (snapshot dữ liệu sản phẩm tại thời điểm đặt)
CREATE TABLE `order_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Tham chiếu sản phẩm (NULL nếu SP bị xóa)',
    `product_name` VARCHAR(255) NOT NULL COMMENT 'Tên SP snapshot',
    `size_name` VARCHAR(255) DEFAULT NULL COMMENT 'Tên size snapshot',
    `ice_level` VARCHAR(255) DEFAULT NULL,
    `sugar_level` VARCHAR(255) DEFAULT NULL,
    `quantity` INT UNSIGNED NOT NULL,
    `unit_price` DECIMAL(12,0) NOT NULL COMMENT 'Đơn giá tại thời điểm đặt',
    `subtotal` DECIMAL(12,0) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `order_items_order_id_index` (`order_id`),
    KEY `order_items_product_id_index` (`product_id`),
    CONSTRAINT `order_items_order_id_foreign`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `order_items_product_id_foreign`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.3 Topping trong đơn hàng (snapshot)
CREATE TABLE `order_item_topping` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_item_id` BIGINT UNSIGNED NOT NULL,
    `topping_name` VARCHAR(255) NOT NULL COMMENT 'Tên topping snapshot',
    `price` DECIMAL(12,0) NOT NULL COMMENT 'Giá topping snapshot',
    PRIMARY KEY (`id`),
    KEY `order_item_topping_order_item_id_index` (`order_item_id`),
    CONSTRAINT `order_item_topping_order_item_id_foreign`
        FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. ĐÁNH GIÁ (theo đơn hàng)
-- ============================================================

CREATE TABLE `reviews` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'NULL - đánh giá theo đơn hàng, không theo SP',
    `order_id` BIGINT UNSIGNED NOT NULL,
    `rating` TINYINT NOT NULL COMMENT '1-5 sao',
    `comment` TEXT DEFAULT NULL,
    `is_approved` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Admin duyệt mới hiển thị',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `reviews_user_order_unique` (`user_id`, `order_id`) COMMENT '1 user chỉ đánh giá 1 lần/đơn',
    KEY `reviews_order_id_index` (`order_id`),
    CONSTRAINT `reviews_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `reviews_product_id_foreign`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
    CONSTRAINT `reviews_order_id_foreign`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. SỬ DỤNG COUPON (lịch sử)
-- ============================================================

CREATE TABLE `coupon_usages` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `coupon_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `discount_amount` DECIMAL(12,0) NOT NULL COMMENT 'Số tiền thực tế được giảm',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `coupon_usages_coupon_user_index` (`coupon_id`, `user_id`),
    CONSTRAINT `coupon_usages_coupon_id_foreign`
        FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
    CONSTRAINT `coupon_usages_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `coupon_usages_order_id_foreign`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. CHƯƠNG TRÌNH KHÁCH HÀNG THÂN THIẾT (LOYALTY)
-- ============================================================

CREATE TABLE `point_transactions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `order_id` BIGINT UNSIGNED DEFAULT NULL,
    `type` ENUM('earn', 'redeem') NOT NULL COMMENT 'Tích điểm hoặc tiêu điểm',
    `points` INT NOT NULL COMMENT 'Số điểm (dương = tích, âm = tiêu)',
    `description` VARCHAR(255) NOT NULL COMMENT 'Mô tả giao dịch',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `point_transactions_user_created_index` (`user_id`, `created_at`),
    CONSTRAINT `point_transactions_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `point_transactions_order_id_foreign`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. BẢNG MIGRATIONS (Laravel internal)
-- ============================================================

CREATE TABLE `migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- TỔNG KẾT CƠ SỞ DỮ LIỆU
-- ============================================================
-- Bảng hệ thống Laravel : 8 (users, password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches, failed_jobs, migrations)
-- Bảng nghiệp vụ chính  : 12 (categories, products, product_images, sizes, toppings, coupons, addresses, carts, cart_items, orders, order_items, reviews)
-- Bảng pivot             : 4 (product_size, product_topping, cart_item_topping, order_item_topping)
-- Bảng lịch sử/tracking  : 2 (coupon_usages, point_transactions)
-- TỔNG                   : 26 bảng
--
-- LUỒNG NGHIỆP VỤ:
-- 1. Quản lý sản phẩm    : categories → products → product_images, product_size, product_topping
-- 2. Giỏ hàng             : carts → cart_items → cart_item_topping
-- 3. Đặt hàng             : orders → order_items → order_item_topping
-- 4. Mã giảm giá          : coupons → coupon_usages
-- 5. Đánh giá             : reviews (theo đơn hàng, admin duyệt)
-- 6. Khách hàng thân thiết: users(loyalty_points, total_points_earned) → point_transactions
-- 7. Địa chỉ giao hàng   : addresses
--
-- HẠNG THÀNH VIÊN (tính từ total_points_earned):
-- Bronze  : 0+ điểm     (x1.0)
-- Silver  : 50+ điểm    (x1.2)
-- Gold    : 200+ điểm   (x1.5)
-- Diamond : 500+ điểm   (x2.0)
--
-- QUY ĐỔI ĐIỂM:
-- Tích: 10.000 VNĐ = 1 điểm (nhân hệ số hạng)
-- Tiêu: 1 điểm = 1.000 VNĐ (tối đa 30% subtotal)

# Coffee Shop - Hệ thống quản lý quán cà phê

## Giới thiệu

Hệ thống quản lý quán cà phê trực tuyến, cho phép khách hàng đặt hàng online và quản trị viên quản lý toàn bộ hoạt động kinh doanh. Dự án được xây dựng theo mô hình Single Page Application (SPA) với giao diện hiện đại, responsive trên mọi thiết bị.

---

## Công nghệ sử dụng

### Backend
| Công nghệ | Phiên bản | Mô tả |
|------------|-----------|-------|
| PHP | ^8.2 | Ngôn ngữ lập trình phía server |
| Laravel | 12 | PHP Framework, xử lý routing, ORM, validation, authentication |
| Laravel Sanctum | 4.0 | Xác thực API qua session (SPA authentication) |
| Laravel Breeze | - | Scaffolding cho đăng ký, đăng nhập, quên mật khẩu |
| Inertia.js (server) | 2.0 | Cầu nối giữa Laravel và Vue.js, render SPA không cần API riêng |
| Ziggy | 2.0 | Cho phép sử dụng Laravel named routes trong JavaScript |
| MySQL | - | Hệ quản trị cơ sở dữ liệu quan hệ |
| PayOS SDK | 2.0 | Cổng thanh toán trực tuyến qua VietQR |

### Frontend
| Công nghệ | Phiên bản | Mô tả |
|------------|-----------|-------|
| Vue.js | 3.4 | JavaScript framework cho giao diện người dùng |
| Inertia.js (client) | 2.0 | Điều hướng SPA, chia sẻ dữ liệu giữa server và client |
| Tailwind CSS | 4.2 | Utility-first CSS framework |
| Vite | 7.0 | Build tool, hot module replacement trong quá trình phát triển |
| Axios | 1.11 | HTTP client cho các API request (giỏ hàng, admin CRUD) |

### Công cụ phát triển
| Công nghệ | Mô tả |
|------------|-------|
| Composer | Quản lý thư viện PHP |
| NPM | Quản lý thư viện JavaScript |
| Laravel Pint | Định dạng code PHP theo chuẩn |
| PHPUnit | Unit testing |
| XAMPP | Môi trường phát triển local (Apache + MySQL + PHP) |

---

## Kiến trúc hệ thống

```
┌─────────────────────────────────────────────────────┐
│                    TRÌNH DUYỆT                      │
│  Vue 3 + Inertia.js Client + Tailwind CSS           │
└──────────────────────┬──────────────────────────────┘
                       │ HTTP Request
┌──────────────────────▼──────────────────────────────┐
│                   LARAVEL 12                        │
│  ┌─────────────┐  ┌──────────────┐  ┌────────────┐  │
│  │  Middleware │→ │  Controller  │→ │   Service  │  │
│  │  (Auth,     │  │  (Web/API)   │  │   Layer    │  │
│  │   Role)     │  └──────┬───────┘  └─────┬──────┘  │
│  └─────────────┘         │                │         │
│                   ┌──────▼────────────────▼──────┐  │
│                   │     Eloquent ORM (Models)    │  │
│                   └──────────────┬───────────────┘  │
└──────────────────────────────────┼──────────────────┘
                                   │
                        ┌──────────▼──────────┐
                        │       MySQL         │
                        │    (26 bảng)        │
                        └─────────────────────┘
```

---

## Phân quyền người dùng

| Vai trò | Quyền hạn |
|---------|-----------|
| **Customer** (Khách hàng) | Duyệt menu, đặt hàng, thanh toán, đánh giá, quản lý địa chỉ, tích/đổi điểm thưởng, xem lịch sử đơn hàng |
| **Staff** (Nhân viên) | Toàn bộ quyền quản trị ngoại trừ quản lý tài khoản người dùng |
| **Admin** (Quản trị viên) | Toàn quyền: quản lý sản phẩm, đơn hàng, mã giảm giá, nhân viên, báo cáo thống kê |

---

## Chức năng chi tiết

### 1. Xác thực & Tài khoản

| Chức năng | Mô tả |
|-----------|-------|
| Đăng ký | Đăng ký bằng họ tên, số điện thoại (bắt buộc), email (tùy chọn), mật khẩu |
| Đăng nhập | Đăng nhập bằng email hoặc số điện thoại + mật khẩu |
| Quên mật khẩu | Gửi email đặt lại mật khẩu |
| Quản lý hồ sơ | Cập nhật thông tin cá nhân, đổi mật khẩu, xóa tài khoản |
| Validation | SĐT phải đúng định dạng VN (03x, 05x, 07x, 08x, 09x + 8 số), email đúng format, cảnh báo lỗi real-time |

### 2. Trang chủ & Menu

| Chức năng | Mô tả |
|-----------|-------|
| Trang chủ | Banner hero, danh sách sản phẩm nổi bật (tối đa 8), danh mục |
| Menu | Duyệt toàn bộ sản phẩm với phân trang (12 SP/trang) |
| Lọc & tìm kiếm | Lọc theo danh mục, tìm kiếm theo tên, sắp xếp theo giá/tên/mới nhất |
| Chi tiết sản phẩm | Gallery nhiều ảnh, chọn size/mức đá/mức đường/topping, xem đánh giá |

### 3. Giỏ hàng

| Chức năng | Mô tả |
|-----------|-------|
| Thêm sản phẩm | Thêm vào giỏ với size, mức đá, mức đường, topping, số lượng |
| Cập nhật | Thay đổi số lượng từng sản phẩm |
| Xóa | Xóa từng sản phẩm hoặc xóa toàn bộ giỏ |
| Hỗ trợ khách vãng lai | Giỏ hàng lưu theo session, tự merge khi đăng nhập |

### 4. Đặt hàng & Thanh toán

| Chức năng | Mô tả |
|-----------|-------|
| Loại đơn | Giao hàng (Delivery) hoặc tự đến lấy (Pickup) |
| Thông tin giao hàng | Nhập tên, SĐT, địa chỉ; chọn từ địa chỉ đã lưu hoặc nhập mới |
| Lưu địa chỉ | Tùy chọn lưu địa chỉ mới cho lần đặt sau |
| Áp mã giảm giá | Nhập mã coupon, hệ thống tự tính giảm giá |
| Dùng điểm thưởng | Đổi điểm tích lũy để giảm giá (tối đa 50% subtotal, 1 điểm = 1.000 VNĐ) |
| Thanh toán | COD (tiền mặt khi nhận hàng), Chuyển khoản ngân hàng, hoặc Thanh toán QR qua PayOS |
| Mã đơn hàng | Tự sinh mã duy nhất dạng CFxxxxxx |

### 5. Quản lý đơn hàng (Khách hàng)

| Chức năng | Mô tả |
|-----------|-------|
| Danh sách đơn | Xem tất cả đơn hàng, lọc theo trạng thái |
| Chi tiết đơn | Xem sản phẩm, giá, trạng thái, thông tin giao hàng |
| Hủy đơn | Hủy đơn khi chưa xác nhận (trạng thái Pending) |
| Đánh giá | Đánh giá đơn hàng đã hoàn thành (1-5 sao + nhận xét), mỗi user chỉ đánh giá 1 lần/đơn |

### 6. Chương trình khách hàng thân thiết (Loyalty)

| Hạng | Điểm tối thiểu | Hệ số tích điểm |
|------|----------------|-----------------|
| Bronze | 0 | x1.0 |
| Silver | 50 | x1.2 |
| Gold | 200 | x1.5 |
| Diamond | 500 | x2.0 |

- **Tích điểm**: Mỗi 10.000 VNĐ = 1 điểm (nhân hệ số hạng), tự động khi đơn hoàn thành
- **Đổi điểm**: 1 điểm = 1.000 VNĐ, tối đa 30% giá trị đơn hàng
- **Lịch sử**: Xem toàn bộ giao dịch tích/đổi điểm

### 7. Quản trị hệ thống (Admin/Staff)

#### 7.1 Dashboard
- Thống kê tổng quan: doanh thu, số đơn, khách hàng mới, sản phẩm bán chạy

#### 7.2 Quản lý danh mục
| Thao tác | Mô tả |
|----------|-------|
| Thêm | Tạo danh mục mới (tên, thứ tự, trạng thái) |
| Sửa | Cập nhật thông tin danh mục |
| Xóa | Xóa danh mục (chỉ khi không còn sản phẩm) |

#### 7.3 Quản lý sản phẩm
| Thao tác | Mô tả |
|----------|-------|
| Thêm | Tên, danh mục, mô tả, giá, nhiều ảnh (ảnh chính + phụ), size & giá, topping |
| Sửa | Cập nhật mọi thông tin, thêm/xóa ảnh, đặt ảnh chính |
| Xóa | Soft delete (sản phẩm ẩn đi, dữ liệu đơn hàng cũ vẫn giữ) |
| Trạng thái | Bật/tắt bán, đánh dấu nổi bật |

#### 7.4 Quản lý đơn hàng
| Thao tác | Mô tả |
|----------|-------|
| Danh sách | Xem tất cả đơn, lọc theo trạng thái, phân trang |
| Cập nhật trạng thái | Chuyển trạng thái theo luồng: Chờ → Xác nhận → Đang pha → Sẵn sàng → Đang giao → Hoàn thành |
| Hủy đơn | Hủy đơn với lý do |
| Thông báo đơn mới | Polling tự động kiểm tra đơn hàng mới |

#### 7.5 Quản lý mã giảm giá (Coupon)
| Thao tác | Mô tả |
|----------|-------|
| Thêm | Mã, tên, loại (% hoặc cố định), giá trị, đơn tối thiểu, giảm tối đa, giới hạn lượt dùng, thời hạn |
| Sửa/Xóa | Cập nhật hoặc xóa mã giảm giá |

#### 7.6 Quản lý Size & Topping
- CRUD kích thước đồ uống (S, M, L) và topping (Trân châu, Thạch, ...)

#### 7.7 Quản lý đánh giá
| Thao tác | Mô tả |
|----------|-------|
| Duyệt | Phê duyệt đánh giá để hiển thị công khai |
| Xóa | Xóa đánh giá không phù hợp |

#### 7.8 Quản lý người dùng (chỉ Admin)
- Xem danh sách, cập nhật vai trò (Customer/Staff/Admin)

#### 7.9 Báo cáo & Thống kê
| Báo cáo | Nội dung |
|---------|----------|
| Doanh thu | Tổng doanh thu theo khoảng thời gian |
| Đơn hàng | Số lượng đơn theo trạng thái |
| Sản phẩm | Top sản phẩm bán chạy |
| Khách hàng | Top khách hàng chi tiêu nhiều nhất |

---

## Luồng hoạt động chính

### Luồng 1: Đặt hàng

```
Khách hàng duyệt Menu
        │
        ▼
Chọn sản phẩm → Tùy chỉnh (size, đá, đường, topping)
        │
        ▼
Thêm vào giỏ hàng ──→ (Có thể tiếp tục mua)
        │
        ▼
Vào giỏ hàng → Kiểm tra & điều chỉnh số lượng
        │
        ▼
Thanh toán (Checkout)
  ├── Chọn loại đơn (Giao/Tự lấy)
  ├── Nhập/chọn địa chỉ giao hàng
  ├── Áp mã giảm giá (tùy chọn)
  ├── Dùng điểm thưởng (tùy chọn)
  └── Chọn phương thức thanh toán
        │
        ▼
Xác nhận đặt hàng → Tạo đơn hàng (trạng thái: Chờ xử lý)
        │
        ▼
Admin/Staff xử lý đơn
  Chờ → Xác nhận → Đang pha → Sẵn sàng → Đang giao → Hoàn thành
        │
        ▼
Tự động tích điểm thưởng khi đơn hoàn thành
```

### Luồng 2: Đánh giá

```
Đơn hàng hoàn thành (Completed)
        │
        ▼
Khách vào chi tiết đơn → Nhấn "Đánh giá"
        │
        ▼
Chọn số sao (1-5) + Nhập nhận xét
        │
        ▼
Gửi đánh giá → Chờ Admin duyệt
        │
        ▼
Admin duyệt → Hiển thị trên trang chi tiết sản phẩm
  (Đánh giá theo đơn hàng, hiển thị trên tất cả SP trong đơn)
```

### Luồng 3: Tích & đổi điểm thưởng

```
Đơn hàng hoàn thành
        │
        ▼
Tính điểm: (Tổng tiền / 10.000) × Hệ số hạng
        │
        ▼
Cộng vào loyalty_points (dùng được) và total_points_earned (xếp hạng)
        │
        ▼
Khi đặt hàng mới → Chọn số điểm muốn đổi
  (Tối đa 30% subtotal, 1 điểm = 1.000 VNĐ)
        │
        ▼
Trừ điểm → Giảm giá đơn hàng
```

### Luồng 4: Mã giảm giá

```
Admin tạo mã giảm giá (code, loại, giá trị, điều kiện)
        │
        ▼
Khách nhập mã tại Checkout → Hệ thống validate:
  ├── Mã có tồn tại và đang hoạt động?
  ├── Trong thời hạn?
  ├── Còn lượt sử dụng?
  └── Đơn hàng đạt giá trị tối thiểu?
        │
        ▼
Áp dụng → Tính giảm giá (% hoặc cố định, có mức tối đa)
        │
        ▼
Lưu lịch sử sử dụng (coupon_usages), tăng used_count
```

---

## Cơ sở dữ liệu

Tổng cộng **26 bảng**, chia thành 4 nhóm:

### Bảng hệ thống Laravel (9 bảng)
`users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations`

### Bảng nghiệp vụ chính (12 bảng)
`categories`, `products`, `product_images`, `sizes`, `toppings`, `coupons`, `addresses`, `carts`, `cart_items`, `orders`, `order_items`, `reviews`

### Bảng pivot (4 bảng)
`product_size`, `product_topping`, `cart_item_topping`, `order_item_topping`

### Bảng lịch sử/tracking (2 bảng)
`coupon_usages`, `point_transactions`

### Sơ đồ quan hệ chính

```
users ──┬── addresses (1:N)
        ├── orders (1:N) ──┬── order_items (1:N) ── order_item_topping (1:N)
        │                  ├── reviews (1:N)
        │                  └── coupon_usages (1:N)
        ├── carts (1:1) ── cart_items (1:N) ── cart_item_topping (1:N)
        ├── reviews (1:N)
        └── point_transactions (1:N)

categories ── products (1:N) ──┬── product_images (1:N)
                               ├── product_size (N:M) ── sizes
                               ├── product_topping (N:M) ── toppings
                               └── order_items (1:N)

coupons ──┬── orders (1:N)
          └── coupon_usages (1:N)
```

---

## Cấu trúc thư mục chính

```
doantotnghiep/
├── app/
│   ├── Enums/              # 8 enum: UserRole, OrderStatus, LoyaltyTier, ...
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/        # API controllers (Cart, Checkout, Order, Admin CRUD)
│   │   │   ├── Auth/       # Đăng ký, đăng nhập, quên mật khẩu
│   │   │   └── Web/        # Trang Inertia (Home, Menu, Cart, Admin pages)
│   │   ├── Middleware/     # EnsureIsAdmin, EnsureIsAdminOrStaff
│   │   └── Requests/      # Form Request validation
│   ├── Models/             # 16 Eloquent models
│   └── Services/           # 6 service classes (Cart, Order, Pricing, Coupon, Loyalty, Report)
├── database/
│   ├── migrations/         # Schema migrations
│   ├── seeders/            # Dữ liệu mẫu (Users, Categories, Products, ...)
│   └── coffee_shop.sql     # File SQL xây dựng toàn bộ CSDL
├── resources/js/
│   ├── Composables/        # Vue composables (useCart, useToast, useFormatters)
│   ├── Layouts/            # AppLayout (khách), AdminLayout (quản trị), GuestLayout (auth)
│   └── Pages/              # Trang Vue.js theo cấu trúc Inertia
│       ├── Admin/          # Dashboard, Products, Orders, Categories, ...
│       ├── Auth/           # Login, Register, ForgotPassword, ...
│       ├── Cart/           # Giỏ hàng
│       ├── Checkout/       # Thanh toán
│       ├── Menu/           # Danh sách & chi tiết sản phẩm
│       ├── Orders/         # Lịch sử đơn hàng
│       └── Home.vue        # Trang chủ
├── routes/
│   ├── web.php             # Routes trang web (Inertia)
│   ├── api.php             # Routes API (giỏ hàng, admin CRUD)
│   └── auth.php            # Routes xác thực (Breeze)
└── public/
    ├── images/             # Ảnh tĩnh (banner, ...)
    └── storage/            # Symlink đến storage/app/public (ảnh sản phẩm upload)
```

---

## Tài khoản thử nghiệm

| Vai trò | Email | Số điện thoại | Mật khẩu |
|---------|-------|---------------|-----------|
| Admin | admin@coffee.test | 0901234567 | password |
| Staff | staff@coffee.test | 0907654321 | password |
| Customer | customer@coffee.test | 0912345678 | password |

---

## Cài đặt & Chạy dự án

```bash
# 1. Clone và cài đặt dependencies
composer install
npm install

# 2. Cấu hình môi trường
cp .env.example .env
php artisan key:generate

# 3. Cấu hình database trong .env
# DB_DATABASE=coffee_shop
# DB_USERNAME=root
# DB_PASSWORD=

# 3b. Cấu hình PayOS (thanh toán QR) trong .env
# PAYOS_CLIENT_ID=your_client_id
# PAYOS_API_KEY=your_api_key
# PAYOS_CHECKSUM_KEY=your_checksum_key

# 4. Chạy migration và seed dữ liệu mẫu
php artisan migrate --seed

# 5. Tạo symlink cho storage
php artisan storage:link

# 6. Build frontend
npm run build

# 7. Chạy server
php artisan serve
```

Truy cập: `http://localhost:8000`

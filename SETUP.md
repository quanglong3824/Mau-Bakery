# 🎂 Hướng Dẫn Cài Đặt - Mâu Bakery

Chào mừng bạn đến với dự án **Mâu Bakery**! Đây là dự án website bán bánh ngọt đơn giản, phù hợp để sinh viên tham khảo cấu trúc PHP thuần và mô hình MVC cơ bản.

---

## 🛠 Yêu Cầu Hệ Thống

- **XAMPP** (hoặc WAMP/MAMP/LAMP stack bất kỳ).
- **PHP**: Phiên bản 7.4 trở lên (Khuyến nghị 8.0+).
- **MySQL/MariaDB**.

---

## 🚀 Các Bước Cài Đặt

### Bước 1: Chuẩn bị Source Code

1.  Tải source code về máy.
2.  Giải nén và copy thư mục dự án vào thư mục `htdocs` của XAMPP.
    - Đường dẫn thường là: `C:\xampp\htdocs\MauBakery`

### Bước 2: Thiết Lập Cơ Sở Dữ Liệu (Database)

File SQL đã được cấu hình trọn gói (bao gồm lệnh `CREATE DATABASE`). Bạn chỉ cần chạy nó!

**Cách 1: Import file (Khuyên dùng)**

1.  Truy cập **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2.  Ở trang chủ (chưa chọn database nào), bấm vào tab **Import** (Nhập).
3.  Chọn file `.sql` mới nhất nằm trong thư mục `docs/`.
4.  Bấm **Go** (Thực hiện). Hệ thống sẽ tự động tạo database `MauBakery` và nhập dữ liệu -> **Xanh (Thành công)**.

**Cách 2: Copy - Paste ( Nhanh nhất )**

1.  Mở file `.sql` trong thư mục `docs/` bằng trình soạn thảo (Notepad/VSCode).
2.  Copy toàn bộ nội dung.
3.  Vào phpMyAdmin, bấm chọn tab **SQL**.
4.  Dán code vào và bấm **Go**. Tất cả sẽ xanh!

### Bước 3: Cấu Hình Kết Nối (Nếu cần)

Mặc định dự án đã cấu hình chuẩn cho XAMPP. Nếu bạn dùng mật khẩu MySQL khác, hãy sửa file:
📂 `config/db.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // Tên đăng nhập (XAMPP mặc định là root)
define('DB_PASS', '');         // Mật khẩu (XAMPP mặc định để trống)
define('DB_NAME', 'MauBakery'); // Tên database bạn vừa tạo
```

### Bước 4: Chạy Dự Án

- **Trang chủ khách hàng**: [http://localhost/MauBakery](http://localhost/MauBakery) (hoặc tên thư mục bạn đặt).
- **Trang quản trị (Admin)**: [http://localhost/MauBakery/admin/login.php](http://localhost/MauBakery/admin/login.php)

---

## 🔑 Tài Khoản Quản Trị (Admin)

Mặc định trong file SQL đã có sẵn tài khoản Admin:

- **Username**: `admin`
- **Password**: `123456` (Nếu không đăng nhập được, xem hướng dẫn reset bên dưới).

---

## 💡 Hướng Dẫn Sử Dụng Cơ Bản

### 1. Dành cho Khách Hàng

- Xem danh sách bánh, tìm kiếm, lọc theo giá.
- Thêm vào giỏ hàng (Gio hàng lưu trong Session).
- Đăng ký/Đăng nhập thành viên.
- Đặt hàng (Checkout).
- Xem lại lịch sử đơn hàng.

### 2. Dành cho Admin

- **Thống kê Dashboard**: Xem tổng quan doanh thu, đơn hàng.
- **Quản lý Đơn hàng**: Xem chi tiết, cập nhật trạng thái (Đang giao, Hoàn thành, Hủy...).
- **Quản lý Sản phẩm**: Thêm, Sửa, Xóa, Ẩn/Hiện sản phẩm.
- **Thư viện ảnh**: Upload ảnh bánh, đổi tên file (Hệ thống tự động xử lý tên tiếng Việt).
- **Backup Dữ liệu**: Vào phần Cài đặt để tải về file SQL backup toàn bộ hệ thống.

---

## ❓ Xử Lý Sự Cố Thường Gặp

**1. Lỗi "Connection failed"**

- Kiểm tra lại xem MySQL trong XAMPP đã Bật (Start) chưa.
- Kiểm tra lại file `config/db.php` xem user/pass đúng chưa.

**2. Lỗi Font chữ / Tiếng Việt bị lỗi**

- Đảm bảo lúc tạo Database chọn `utf8mb4_general_ci`.

**3. Không đăng nhập được Admin**

- Vào phpMyAdmin > bảng `users`.
- Tạo một tài khoản mới ngoài trang đăng ký.
- Trong bảng `users`, tìm dòng tài khoản đó, sửa cột `role` từ `user` thành `admin`.
- Bây giờ bạn có thể đăng nhập Admin bằng tài khoản đó.

**4. Ảnh không hiển thị**

- Kiểm tra thư mục `uploads/`. Đảm bảo trong `config` hoặc đường dẫn ảnh trong database khớp với tên file thực tế.

---

_Chúc các bạn học tập tốt với dự án Mâu Bakery!_ 🍰

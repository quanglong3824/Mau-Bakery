<?php
// admin/settings.php

require_once 'includes/auth_check.php';
require_once '../config/db.php';

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'clear_transactions') {
        try {
            $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
            $tables = ['order_items', 'orders', 'reviews', 'contacts', 'favorites'];
            foreach ($tables as $table) {
                $conn->exec("TRUNCATE TABLE `$table`");
            }
            $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
            $msg = "Đã dọn dẹp sạch toàn bộ dữ liệu giao dịch!";
        } catch (PDOException $e) {
            $err = "Lỗi: " . $e->getMessage();
        }
    }

    if ($action === 'force_reset') {
        try {
            $conn->exec("SET FOREIGN_KEY_CHECKS = 0");

            // 1. Truncate Content Tables
            $tables = ['products', 'categories', 'posts', 'faqs', 'order_items', 'orders', 'reviews', 'contacts', 'favorites'];
            foreach ($tables as $table) {
                $conn->exec("TRUNCATE TABLE `$table`");
            }

            // 2. Delete non-admin users
            // Reset Auto Increment if possible, or just delete.
            $conn->exec("DELETE FROM users WHERE role != 'admin'");

            $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

            $msg = "Hệ thống đã được RESET toàn bộ về trạng thái ban đầu (Chỉ giữ lại Admin)!";
        } catch (PDOException $e) {
            $err = "Lỗi Reset: " . $e->getMessage();
        }
    }

    if ($action === 'export_db') {
        $filename = 'backup_maubakery_' . date('Y-m-d_H-i') . '.sql';
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Initial SQL
        echo "-- Database Backup: Mâu Bakery\n";
        echo "-- Date: " . date('Y-m-d H:i:s') . "\n";
        echo "-- Exported by: " . $_SESSION['username'] . "\n\n";

        // Create Database if not exists
        echo "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET " . DB_CHARSET . " COLLATE " . DB_CHARSET . "_general_ci;\n";
        echo "USE `" . DB_NAME . "`;\n\n";

        echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Structure
            $row = $conn->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
            echo "\n\n" . $row[1] . ";\n\n";

            // Data
            $rows = $conn->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $cols = array_keys($row);
                $vals = array_values($row);

                // Escape values
                $vals = array_map(function ($v) use ($conn) {
                    if ($v === null)
                        return "NULL";
                    return $conn->quote($v);
                }, $vals);

                echo "INSERT INTO `$table` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(", ", $vals) . ");\n";
            }
        }

        echo "\nSET FOREIGN_KEY_CHECKS=1;\n";
        exit;
    }
}

include 'includes/header.php';
?>

<div class="header-bar">
    <h1 class="page-title">Cài Đặt & Dữ Liệu</h1>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $msg; ?></div>
<?php endif; ?>
<?php if ($err): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $err; ?></div>
<?php endif; ?>

<div class="glass-panel" style="max-width: 800px;">

    <!-- Section 0: Backup -->
    <div class="mb-5 pb-4 border-bottom">
        <h2 class="text-secondary mb-3"><i class="fas fa-database"></i> Sao Lưu Dữ Liệu</h2>
        <p class="text-muted mb-3">
            Tải xuống bản sao lưu toàn bộ cơ sở dữ liệu (.sql). <br>
            Bạn nên thực hiện việc này thường xuyên để tránh mất dữ liệu.
        </p>
        <form method="POST">
            <input type="hidden" name="action" value="export_db">
            <button class="btn btn-primary text-white">
                <i class="fas fa-download"></i> Tải Về SQL Backup
            </button>
        </form>
    </div>

    <!-- Section 1: Clear Transactions -->
    <div class="mb-5 pb-4 border-bottom">
        <h2 class="text-secondary mb-3"><i class="fas fa-broom"></i> Dọn Dẹp Giao Dịch</h2>
        <p class="text-muted mb-3">
            Xóa toàn bộ: <b>Đơn hàng, Đánh giá, Liên hệ, Yêu thích</b>. <br>
            Giữ lại: Sản phẩm, Danh mục, Tin tức, Người dùng.
        </p>
        <form method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa dữ liệu giao dịch?');">
            <input type="hidden" name="action" value="clear_transactions">
            <button class="btn btn-warning text-white">
                <i class="fas fa-eraser"></i> Xóa Giao Dịch Rác
            </button>
        </form>
    </div>

    <!-- Section 2: Danger Zone -->
    <div>
        <h2 class="text-danger mb-3"><i class="fas fa-radiation"></i> Vùng Nguy Hiểm: RESET HỆ THỐNG</h2>
        <div class="alert alert-danger">
            <strong>CẢNH BÁO CỰC KỲ NGHIÊM TRỌNG:</strong><br>
            Hành động này sẽ xóa sạch <b>TOÀN BỘ CƠ SỞ DỮ LIỆU</b> về trắng.
            <ul class="my-2">
                <li>Xóa tất cả Sản Phẩm, Danh Mục.</li>
                <li>Xóa tất cả Bài viết, FAQs.</li>
                <li>Xóa tất cả Khách hàng (Chỉ giữ lại tài khoản Admin đang đăng nhập).</li>
                <li>Xóa tất cả Đơn hàng, Đánh giá...</li>
            </ul>
            Dữ liệu sẽ không thể phục hồi. Hãy cân nhắc kỹ!
        </div>
        <form method="POST"
            onsubmit="return confirm('CẢNH BÁO CUỐI CÙNG:\n\nBạn đang chuẩn bị XÓA SẠCH toàn bộ dữ liệu hệ thống (Sản phẩm, Danh mục, Khách hàng...).\n\nChỉ giữ lại tài khoản Admin.\n\nBạn có chắc chắn 100% không?');">
            <input type="hidden" name="action" value="force_reset">
            <button class="btn btn-danger btn-glass">
                <i class="fas fa-skull-crossbones"></i> XÓA TRẮNG TOÀN BỘ HỆ THỐNG
            </button>
        </form>
    </div>

</div>

<div class="glass-panel mt-4" style="max-width: 800px;">
    <h2 class="mb-3"><i class="fas fa-info-circle"></i> Thông Tin Server</h2>
    <div class="p-3" style="background: #f8fafc; border-radius: 8px;">
        <p class="mb-2"><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
        <p class="mb-2"><strong>Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
        <p class="mb-0"><strong>Database:</strong> MySQL</p>
    </div>
</div>

<style>
    .text-secondary {
        color: #57606f;
    }

    .border-bottom {
        border-bottom: 1px solid #eee;
    }

    .btn-warning {
        background: #f39c12;
    }

    .btn-warning:hover {
        background: #e67e22;
    }
</style>

<?php include 'includes/footer.php'; ?>
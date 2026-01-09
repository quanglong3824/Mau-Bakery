<?php
session_start();
// Include Controller
require_once 'controllers/SettingsController.php';

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

<link rel="stylesheet" href="assets/css/settings.css">

<?php include 'includes/footer.php'; ?>
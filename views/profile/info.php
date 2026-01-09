<?php
// views/profile/info.php
?>
<div id="info">
    <h2 class="content-title">Thông Tin Cá Nhân</h2>
    <form class="profile-form" method="POST">
        <input type="hidden" name="action" value="update_info">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="full_name" class="form-input"
                    value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                    style="background: rgba(0,0,0,0.05);">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" name="phone" class="form-input"
                    value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Ngày đăng ký</label>
                <input type="text" class="form-input"
                    value="<?php echo date('d/m/Y', strtotime($user['created_at'])); ?>" disabled>
            </div>
        </div>
        <button type="submit" class="btn-save mt-1">Cập nhật thông tin</button>
    </form>
</div>
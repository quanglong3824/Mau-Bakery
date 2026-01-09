<?php
// views/profile/password.php
?>
<div id="password">
    <h2 class="content-title">Đổi Mật Khẩu</h2>
    <form class="profile-form" method="POST">
        <input type="hidden" name="action" value="change_password">
        <div class="form-group">
            <label class="form-label">Mật khẩu hiện tại</label>
            <input type="password" name="current_password" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label">Mật khẩu mới</label>
            <input type="password" name="new_password" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" name="confirm_password" class="form-input" required>
        </div>
        <button type="submit" class="btn-save mt-1">Đổi mật khẩu</button>
    </form>
</div>
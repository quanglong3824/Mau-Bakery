<aside class="glass-panel profile-sidebar" style="padding: 30px 20px;">
    <div class="user-card">
        <img src="<?php echo !empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['full_name']); ?>"
            class="user-avatar" alt="Avatar">
        <h3 class="user-name">
            <?php echo htmlspecialchars($user['full_name']); ?>
        </h3>
        <span class="user-email">
            <?php echo htmlspecialchars($user['email']); ?>
        </span>
    </div>

    <nav class="profile-nav">
        <a href="index.php?page=profile&tab=dashboard"
            class="nav-item <?php echo $tab === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> Tổng quan
        </a>
        <a href="index.php?page=profile&tab=info" class="nav-item <?php echo $tab === 'info' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i> Thông tin cá nhân
        </a>
        <a href="index.php?page=profile&tab=orders" class="nav-item <?php echo $tab === 'orders' ? 'active' : ''; ?>">
            <i class="fas fa-box-open"></i> Lịch sử đơn hàng
        </a>
        <a href="index.php?page=profile&tab=addresses"
            class="nav-item <?php echo $tab === 'addresses' ? 'active' : ''; ?>">
            <i class="fas fa-map-marker-alt"></i> Sổ địa chỉ
        </a>
        <a href="index.php?page=profile&tab=contacts"
            class="nav-item <?php echo $tab === 'contacts' ? 'active' : ''; ?>">
            <i class="fas fa-history"></i> Lịch sử liên hệ
        </a>
        <a href="index.php?page=profile&tab=password"
            class="nav-item <?php echo $tab === 'password' ? 'active' : ''; ?>">
            <i class="fas fa-lock"></i> Đổi mật khẩu
        </a>
        <a href="index.php?logout=true" class="nav-item logout">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
    </nav>
</aside>
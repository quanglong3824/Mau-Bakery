<?php
// Include Profile Controller to fetch Data
require_once 'controllers/ProfileController.php';
?>

<link rel="stylesheet" href="assets/css/profile.css">

<div class="container mt-2 mb-2">
    <div class="profile-layout">

        <!-- Sidebar -->
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
                <button class="nav-item active" data-target="dashboard">
                    <i class="fas fa-th-large"></i> Tổng quan
                </button>
                <button class="nav-item" data-target="info">
                    <i class="fas fa-user"></i> Thông tin cá nhân
                </button>
                <button class="nav-item" data-target="orders">
                    <i class="fas fa-box-open"></i> Lịch sử đơn hàng
                </button>
                <button class="nav-item" data-target="addresses">
                    <i class="fas fa-map-marker-alt"></i> Sổ địa chỉ
                </button>
                <button class="nav-item" data-target="contacts">
                    <i class="fas fa-history"></i> Lịch sử liên hệ
                </button>
                <button class="nav-item" data-target="password">
                    <i class="fas fa-lock"></i> Đổi mật khẩu
                </button>
                <a href="index.php?logout=true" class="nav-item logout">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="profile-content">
            <?php if (!empty($message)): ?>
                <div
                    style="background: #d4edda; color: #155724; padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                <div
                    style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <!-- Tab: Dashboard -->
            <div id="dashboard" class="tab-pane active">
                <h2 class="content-title">Tổng Quan</h2>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #FFD1DC;">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <h4>Tổng đơn hàng</h4>
                            <span class="stat-value"><?php echo count($orders); ?>
                            </span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #FFF9E3; color: #F59E0B;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stat-info">
                            <h4>Đang xử lý</h4>
                            <span class="stat-value">
                                <?php
                                $pending = array_filter($orders, function ($o) {
                                    return $o['status'] == 'pending';
                                });
                                echo count($pending);
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #B19CD9;">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h4>Điểm tích lũy</h4>
                            <span class="stat-value">
                                <?php echo $user['points']; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <h3 style="margin-bottom: 20px;">Đơn hàng gần đây</h3>
                <div class="table-container">
                    <?php if (empty($orders)): ?>
                        <p style="text-align: center; padding: 20px; color: #777;">Bạn chưa có đơn hàng nào.</p>
                    <?php else: ?>
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Ngày đặt</th>
                                    <!-- <th>Sản phẩm</th> -->
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($orders, 0, 3) as $order): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: var(--accent-color);">
                                            <?php echo $order['order_code']; ?>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                                        </td>
                                        <!-- 
                                    <td>
                                        Items logic needed here (separate query or join)
                                        Getting items for list view is expensive, simplified for now.
                                        Xem chi tiết
                                    </td> 
                                    -->
                                        <td style="font-weight: 700;">
                                            <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                                        </td>
                                        <td>
                                            <?php
                                            $status_label = '';
                                            switch ($order['status']) {
                                                case 'pending':
                                                    $status_label = 'Đang xử lý';
                                                    break;
                                                case 'confirmed':
                                                    $status_label = 'Đã xác nhận';
                                                    break;
                                                case 'shipping':
                                                    $status_label = 'Đang giao';
                                                    break;
                                                case 'completed':
                                                    $status_label = 'Hoàn thành';
                                                    break;
                                                case 'failed':
                                                    $status_label = 'Thất bại';
                                                    break;
                                                case 'cancelled':
                                                    $status_label = 'Đã hủy';
                                                    break;
                                            }
                                            ?>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo $status_label; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab: Personal Info -->
            <div id="info" class="tab-pane">
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
                            <input type="email" class="form-input"
                                value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                                style="background: rgba(0,0,0,0.05);">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-input"
                                value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                        <!-- DOB not in DB schema yet, placeholder or add to DB later -->
                        <div class="form-group">
                            <label class="form-label">Ngày đăng ký</label>
                            <input type="text" class="form-input"
                                value="<?php echo date('d/m/Y', strtotime($user['created_at'])); ?>" disabled>
                        </div>
                    </div>
                    <!-- Gender not in DB schema yet -->
                    <!-- Gender field removed as not in DB schema -->
                    <button type="submit" class="btn-save mt-1">Cập nhật thông tin</button>
                </form>
            </div>

            <!-- Tab: Order History -->
            <div id="orders" class="tab-pane">
                <h2 class="content-title">Lịch Sử Đơn Hàng</h2>
                <!-- Filter Tabs (Mock) -->
                <div style="display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto;">
                    <button class="btn-glass btn-primary"
                        style="border-radius: 20px; padding: 5px 15px; font-size: 0.9rem;">Tất cả</button>
                    <button class="btn-glass" style="border-radius: 20px; padding: 5px 15px; font-size: 0.9rem;">Đang xử
                        lý</button>
                    <button class="btn-glass" style="border-radius: 20px; padding: 5px 15px; font-size: 0.9rem;">Hoàn
                        thành</button>
                    <button class="btn-glass" style="border-radius: 20px; padding: 5px 15px; font-size: 0.9rem;">Đã
                        hủy</button>
                </div>

                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Mã Đơn</th>
                                <th>Ngày đặt</th>
                                <th>Sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--accent-color);">
                                        <?php echo $order['order_code']; ?>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td>
                                        <!-- Simplified Item Count -->
                                        <span>Xem chi tiết</span>
                                    </td>
                                    <td style="font-weight: 700;">
                                        <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                                    </td>
                                    <td>
                                        <?php
                                        $status_label = '';
                                        $bg_class = '';
                                        switch ($order['status']) {
                                            case 'pending':
                                                $status_label = 'Đang xử lý';
                                                $bg_class = 'pending';
                                                break;
                                            case 'confirmed':
                                                $status_label = 'Đã xác nhận';
                                                $bg_class = 'confirmed';
                                                break;
                                            case 'shipping':
                                                $status_label = 'Đang giao';
                                                $bg_class = 'shipping';
                                                break;
                                            case 'completed':
                                                $status_label = 'Hoàn thành';
                                                $bg_class = 'completed';
                                                break;
                                            case 'cancelled':
                                                $status_label = 'Đã hủy';
                                                $bg_class = 'cancelled';
                                                break;
                                            default:
                                                $status_label = $order['status'];
                                        }
                                        ?>
                                        <span class="status-badge status-<?php echo $bg_class; ?>">
                                            <?php echo $status_label; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="index.php?page=order_detail&id=<?php echo $order['id']; ?>"
                                            class="btn-glass" style="padding: 5px 10px; font-size: 0.8rem;">Xem</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Addresses -->
            <div id="addresses" class="tab-pane">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="content-title" style="margin-bottom: 0; border: none;">Sổ Địa Chỉ</h2>
                    <button class="btn-glass" style="font-size: 0.9rem;"
                        onclick="document.getElementById('addAddressModal').style.display='flex'">
                        <i class="fas fa-plus"></i> Thêm địa chỉ mới
                    </button>
                </div>

                <div class="address-grid">
                    <?php if (empty($addresses)): ?>
                        <p style="color: #666;">Chưa có địa chỉ nào được lưu.</p>
                    <?php else: ?>
                        <?php foreach ($addresses as $addr): ?>
                            <div class="address-card <?php echo $addr['is_default'] ? 'default' : ''; ?>">
                                <?php if ($addr['is_default']): ?>
                                    <span class="default-badge">Mặc định</span>
                                <?php endif; ?>

                                <h4 style="margin-bottom: 10px;">
                                    <?php echo htmlspecialchars($addr['recipient_name']); ?>
                                </h4>
                                <p style="color: #666; font-size: 0.95rem; margin-bottom: 5px;">
                                    <?php echo htmlspecialchars($addr['address']); ?>
                                </p>
                                <p style="color: #666; font-size: 0.95rem;">SĐT:
                                    <?php echo htmlspecialchars($addr['phone']); ?>
                                </p>

                                <div style="margin-top: 15px; display: flex; gap: 10px;">
                                    <button
                                        style="color: var(--accent-color); border: none; background: none; cursor: pointer; font-weight: 600;">Sửa</button>
                                    <?php if (!$addr['is_default']): ?>
                                        <button
                                            style="color: #ff6b6b; border: none; background: none; cursor: pointer;">Xóa</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab: Contact History -->
            <div id="contacts" class="tab-pane">
                <h2 class="content-title">Lịch Sử Liên Hệ</h2>
                <div class="table-container">
                    <?php if (empty($contacts_history)): ?>
                        <p style="text-align: center; padding: 20px; color: #777;">Bạn chưa gửi liên hệ nào.</p>
                    <?php else: ?>
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nội dung</th>
                                    <th>Ngày gửi</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contacts_history as $contact): ?>
                                    <tr>
                                        <td style="font-weight: 600;">#<?php echo $contact['id']; ?></td>
                                        <td style="max-width: 300px;">
                                            <div style="white-space: pre-wrap; word-break: break-word; font-size: 0.95rem;">
                                                <?php
                                                // Simple truncation or full view
                                                $msg_display = htmlspecialchars($contact['message']);
                                                echo strlen($msg_display) > 100 ? substr($msg_display, 0, 100) . '...' : $msg_display;
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo isset($contact['created_at']) ? date('d/m/Y H:i', strtotime($contact['created_at'])) : 'N/A'; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-pending">Đã gửi</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab: Change Password -->
            <div id="password" class="tab-pane">
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

        </main>
    </div>
</div>

<!-- Add Address Modal -->
<div id="addAddressModal" class="modal-overlay" onclick="if(event.target === this) this.style.display='none'">
    <div class="modal-content">
        <h3 style="margin-bottom: 20px; font-family: 'Quicksand', sans-serif;">Thêm Địa Chỉ Mới</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_address">
            <div class="form-group">
                <label class="form-label">Tên người nhận</label>
                <input type="text" name="recipient_name" class="form-input" required placeholder="Ví dụ: Nguyễn Văn A">
            </div>
            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" name="phone" class="form-input" required placeholder="Ví dụ: 0987123456">
            </div>
            <div class="form-group">
                <label class="form-label">Địa chỉ chi tiết</label>
                <textarea name="address" class="form-input" rows="3" required
                    placeholder="Số nhà, Tên đường, Phường/Xã..."></textarea>
            </div>
            <div class="form-group">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="is_default"> Đặt làm mặc định
                </label>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn-glass"
                    onclick="document.getElementById('addAddressModal').style.display='none'"
                    style="margin-right: 10px;">Hủy</button>
                <button type="submit" class="btn-save">Lưu Địa Chỉ</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/profile.js"></script>
<?php
session_start();
// Include Controller
require_once 'controllers/DashboardController.php';

include 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/index.css">

<div class="header-bar">
    <h1 class="page-title">Tổng Quan Dashboard</h1>
    <div style="color: #6b7280;">
        <?php echo date('d/m/Y l'); ?>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">

    <!-- Card 1 -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <p class="stat-title">Đơn Hàng Hôm Nay</p>
                <h3 class="stat-value">
                    <?php echo $stats['orders']; ?>
                </h3>
            </div>
            <div class="stat-icon-bg bg-blue-light">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>
        <div class="stat-footer">
            <i class="fas fa-arrow-up"></i> Cập nhật liên tục
        </div>
    </div>

    <!-- Card 2 -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <p class="stat-title">Doanh Thu Hôm Nay</p>
                <h3 class="stat-value">
                    <?php echo number_format($stats['revenue'], 0, ',', '.'); ?>đ
                </h3>
            </div>
            <div class="stat-icon-bg bg-green-light">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <p class="stat-title">Sản Phẩm Đang Bán</p>
                <h3 class="stat-value">
                    <?php echo $stats['products']; ?>
                </h3>
            </div>
            <div class="stat-icon-bg bg-pink-light">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <p class="stat-title">Khách Hàng</p>
                <h3 class="stat-value">
                    <?php echo $stats['users']; ?>
                </h3>
            </div>
            <div class="stat-icon-bg bg-orange-light">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

</div>

<!-- Recent Orders Table Preview -->
<div style="background: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="font-size: 1.2rem; font-weight: 700; color: #111827;">Đơn Hàng Mới Nhất</h3>
        <a href="orders.php" style="color: var(--accent-color); font-weight: 600; text-decoration: none;">Xem tất cả</a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 1px solid #e5e7eb;">
                    <th style="padding: 12px; color: #6b7280; font-weight: 600; font-size: 0.9rem;">Mã Đơn</th>
                    <th style="padding: 12px; color: #6b7280; font-weight: 600; font-size: 0.9rem;">Khách Hàng</th>
                    <th style="padding: 12px; color: #6b7280; font-weight: 600; font-size: 0.9rem;">Tổng Tiền</th>
                    <th style="padding: 12px; color: #6b7280; font-weight: 600; font-size: 0.9rem;">Trạng Thái</th>
                    <th style="padding: 12px; color: #6b7280; font-weight: 600; font-size: 0.9rem;">Ngày Đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($recent_orders)) {
                    foreach ($recent_orders as $order) {
                        $status_colors = [
                            'pending' => '#F59E0B',
                            'confirmed' => '#3B82F6',
                            'shipping' => '#8B5CF6',
                            'completed' => '#10B981',
                            'cancelled' => '#EF4444'
                        ];
                        $status_labels = [
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Đã xác nhận',
                            'shipping' => 'Đang giao',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy'
                        ];
                        $color = $status_colors[$order['status']] ?? '#999';
                        $label = $status_labels[$order['status']] ?? $order['status'];
                        ?>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-weight: 600;">#
                                <?php echo $order['order_code']; ?>
                            </td>
                            <td style="padding: 16px 12px;">
                                <?php echo htmlspecialchars($order['recipient_name']); ?>
                            </td>
                            <td style="padding: 16px 12px; font-weight: 600;">
                                <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                            </td>
                            <td style="padding: 16px 12px;">
                                <span
                                    style="background: <?php echo $color; ?>20; color: <?php echo $color; ?>; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    <?php echo $label; ?>
                                </span>
                            </td>
                            <td style="padding: 16px 12px; color: #6b7280;">
                                <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
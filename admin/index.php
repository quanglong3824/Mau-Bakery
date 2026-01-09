<?php
require_once 'includes/auth_check.php';
require_once '../config/db.php'; // Correct path to config

// Fetch Mock Dashboard Stats (or Real if DB ready)
// Real DB Fetching
$stats = [
    'orders' => 0,
    'revenue' => 0,
    'products' => 0,
    'users' => 0
];

if (isset($conn)) {
    // Today's orders
    $stmt = $conn->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()");
    $stats['orders'] = $stmt->fetchColumn();

    // Today's Revenue (Completed)
    $stmt = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed' AND DATE(created_at) = CURDATE()");
    $stats['revenue'] = $stmt->fetchColumn() ?: 0;

    // Total Products
    $stmt = $conn->query("SELECT COUNT(*) FROM products WHERE is_active = 1");
    $stats['products'] = $stmt->fetchColumn();

    // Total Users
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $stats['users'] = $stmt->fetchColumn();
}

include 'includes/header.php';
?>

<div class="header-bar">
    <h1 class="page-title">Tổng Quan Dashboard</h1>
    <div style="color: #6b7280;">
        <?php echo date('d/m/Y l'); ?>
    </div>
</div>

<!-- Stats Grid -->
<div
    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px;">

    <!-- Card 1 -->
    <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 4px;">Đơn Hàng Hôm Nay</p>
                <h3 style="font-size: 1.8rem; font-weight: 700; color: #111827;">
                    <?php echo $stats['orders']; ?>
                </h3>
            </div>
            <div
                style="background: #eff6ff; color: #3b82f6; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>
        <div style="color: #059669; font-size: 0.85rem; font-weight: 600;">
            <i class="fas fa-arrow-up"></i> Cập nhật liên tục
        </div>
    </div>

    <!-- Card 2 -->
    <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 4px;">Doanh Thu Hôm Nay</p>
                <h3 style="font-size: 1.8rem; font-weight: 700; color: #111827;">
                    <?php echo number_format($stats['revenue'], 0, ',', '.'); ?>đ
                </h3>
            </div>
            <div
                style="background: #f0fdf4; color: #22c55e; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 4px;">Sản Phẩm Đang Bán</p>
                <h3 style="font-size: 1.8rem; font-weight: 700; color: #111827;">
                    <?php echo $stats['products']; ?>
                </h3>
            </div>
            <div
                style="background: #fdf2f8; color: #db2777; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>

    <!-- Card 4 -->
    <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 4px;">Khách Hàng</p>
                <h3 style="font-size: 1.8rem; font-weight: 700; color: #111827;">
                    <?php echo $stats['users']; ?>
                </h3>
            </div>
            <div
                style="background: #fff7ed; color: #ea580c; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
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
                if (isset($conn)) {
                    $stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
                    while ($order = $stmt->fetch()) {
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
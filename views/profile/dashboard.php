<?php
// views/profile/dashboard.php
?>
<div id="dashboard">
    <h2 class="content-title">Tổng Quan</h2>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #FFD1DC;">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-info">
                <h4>Tổng đơn hàng</h4>
                <span class="stat-value">
                    <?php echo count($orders); ?>
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
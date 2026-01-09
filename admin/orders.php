<?php
session_start();
// Include Controller
require_once 'controllers/OrderManagerController.php';

include 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/orders.css">

<div class="header-bar">
    <h1 class="page-title">Quản Lý Đơn Hàng</h1>
</div>

<?php if (isset($msg)): ?>
    <div class="alert-custom-green">
        <i class="fas fa-check-circle"></i>
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="glass-panel p-0 overflow-hidden">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Mã Đơn</th>
                <th>Khách Hàng</th>
                <th>Tổng Tiền</th>
                <th>Ngày Đặt</th>
                <th>Trạng Thái</th>
                <th style="text-align: center;">Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <?php
                $status_colors = [
                    'pending' => 'bg-warning',
                    'confirmed' => 'bg-info',
                    'shipping' => 'bg-primary', // using primary for purple-like
                    'completed' => 'bg-success',
                    'cancelled' => 'bg-danger'
                ];
                $status_labels = [
                    'pending' => 'Chờ xử lý',
                    'confirmed' => 'Đã xác nhận',
                    'shipping' => 'Đang giao',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy'
                ];
                $badge_class = $status_colors[$order['status']] ?? 'bg-secondary';

                // Custom override for specific colors if needed, or stick to global badges
                // Let's use custom style for exact match to previous if desired, but global badges are better for consistency.
                // However, shipping was purple. .btn-info is blue.
                // Let's stick to the inline styles for badges if they were specific, OR use the global .badge classes.
                // I will use global .badge classes for consistency.
                ?>
                <tr>
                    <td class="font-bold">#<?php echo $order['order_code']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($order['recipient_name']); ?><br>
                        <small class="text-small-gray">
                            <?php echo $order['recipient_phone']; ?>
                        </small>
                    </td>
                    <td class="text-accent-bold">
                        <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                    </td>
                    <td class="text-gray">
                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                    </td>
                    <td>
                        <span class="badge <?php echo $badge_class; ?>">
                            <?php echo $status_labels[$order['status']] ?? $order['status']; ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="action" value="update_status">

                            <?php if ($order['status'] == 'pending'): ?>
                                <button type="submit" name="new_status" value="confirmed" class="btn btn-sm btn-info"
                                    title="Xác nhận đơn">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php elseif ($order['status'] == 'confirmed'): ?>
                                <button type="submit" name="new_status" value="shipping"
                                    class="btn btn-sm btn-primary bg-purple border-0" title="Giao hàng">
                                    <i class="fas fa-truck"></i>
                                </button>
                            <?php elseif ($order['status'] == 'shipping'): ?>
                                <button type="submit" name="new_status" value="completed" class="btn btn-sm btn-success"
                                    title="Hoàn tất">
                                    <i class="fas fa-flag-checkered"></i>
                                </button>
                            <?php endif; ?>

                            <?php if (!in_array($order['status'], ['completed', 'cancelled'])): ?>
                                <button type="submit" name="new_status" value="cancelled" class="btn btn-sm btn-danger"
                                    title="Hủy đơn" onclick="return confirm('Bạn chắc chắn muốn hủy đơn này?');">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                        </form>

                        <button class="btn btn-sm btn-secondary"
                            onclick="alert('Tính năng xem chi tiết đang phát triển...')" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
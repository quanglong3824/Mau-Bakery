<?php
// admin/orders.php

require_once 'includes/auth_check.php';
require_once '../config/db.php';

// Handle Action (Change Status)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];

    // Validate status
    $valid_statuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        try {
            $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $new_status, 'id' => $order_id]);
            $msg = "Cập nhật trạng thái đơn hàng #ORD-$order_id thành công!";
        } catch (PDOException $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// Fetch Orders
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$orders = $conn->query($sql)->fetchAll();

include 'includes/header.php';
?>

<div class="header-bar">
    <h1 class="page-title">Quản Lý Đơn Hàng</h1>
</div>

<?php if (isset($msg)): ?>
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
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
                    <td style="font-weight: bold;">#<?php echo $order['order_code']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($order['recipient_name']); ?><br>
                        <small style="color: #888;">
                            <?php echo $order['recipient_phone']; ?>
                        </small>
                    </td>
                    <td style="font-weight: bold; color: var(--accent-color);">
                        <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                    </td>
                    <td style="color: #666;">
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
                                <button type="submit" name="new_status" value="shipping" class="btn btn-sm btn-primary"
                                    title="Giao hàng" style="background-color: #9b59b6;">
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
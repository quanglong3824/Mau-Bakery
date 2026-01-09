<?php
// views/profile/orders.php
?>
<div id="orders">
    <h2 class="content-title">Lịch Sử Đơn Hàng</h2>
    <!-- Filter Tabs (Mock) -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto;">
        <button class="btn-glass btn-primary" style="border-radius: 20px; padding: 5px 15px; font-size: 0.9rem;">Tất
            cả</button>
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
                            <a href="index.php?page=order_detail&id=<?php echo $order['id']; ?>" class="btn-glass"
                                style="padding: 5px 10px; font-size: 0.8rem;">Xem</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
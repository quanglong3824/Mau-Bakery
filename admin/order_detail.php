<?php
session_start();
require_once 'controllers/OrderDetailController.php';
include 'includes/header.php';

$status_labels = [
    'pending' => 'Chờ xử lý',
    'confirmed' => 'Đã xác nhận',
    'shipping' => 'Đang giao',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy'
];
$status_colors = [
    'pending' => 'bg-warning',
    'confirmed' => 'bg-info',
    'shipping' => 'bg-primary',
    'completed' => 'bg-success',
    'cancelled' => 'bg-danger'
];
?>

<div class="header-bar">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="orders.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
        <h1 class="page-title">Chi Tiết Đơn Hàng #<?php echo $order['order_code']; ?></h1>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="print_invoice.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn btn-primary" style="background-color: #6366f1; border-color: #6366f1;">
            <i class="fas fa-print"></i> In Hóa Đơn
        </a>
    </div>
</div>

<?php if (isset($msg)): ?>
    <div class="alert-custom-green mb-4">
        <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Order Info -->
    <div class="col-md-8">
        <div class="glass-panel p-4 mb-4">
            <h3 class="mb-3" style="font-size: 1.1rem; font-weight: 700;">Danh Sách Sản Phẩm</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">Đơn giá</th>
                            <th style="text-align: center;">Số lượng</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <?php if ($item['image']): ?>
                                            <img src="../<?php echo $item['image']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        <?php endif; ?>
                                        <div>
                                            <div style="font-weight: 600;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                            <div style="font-size: 0.8rem; color: #6b7280;">Size: <?php echo $item['size']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    x<?php echo $item['quantity']; ?>
                                </td>
                                <td style="text-align: right; vertical-align: middle; font-weight: 600;">
                                    <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; border: none; padding-top: 20px;">Tạm tính:</td>
                            <td style="text-align: right; border: none; padding-top: 20px; font-weight: 600;">
                                <?php 
                                $subtotal = 0;
                                foreach($items as $item) $subtotal += $item['price'] * $item['quantity'];
                                echo number_format($subtotal, 0, ',', '.'); 
                                ?>đ
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right; border: none;">Phí vận chuyển:</td>
                            <td style="text-align: right; border: none; font-weight: 600;">
                                +<?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>đ
                            </td>
                        </tr>
                        <?php if ($order['discount_amount'] > 0): ?>
                        <tr>
                            <td colspan="3" style="text-align: right; border: none;">Giảm giá:</td>
                            <td style="text-align: right; border: none; font-weight: 600; color: #ef4444;">
                                -<?php echo number_format($order['discount_amount'], 0, ',', '.'); ?>đ
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="3" style="text-align: right; border: none; font-size: 1.1rem; font-weight: 700;">Tổng cộng:</td>
                            <td style="text-align: right; border: none; font-size: 1.1rem; font-weight: 700; color: var(--accent-color);">
                                <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="glass-panel p-4">
            <h3 class="mb-3" style="font-size: 1.1rem; font-weight: 700;">Ghi chú từ khách hàng</h3>
            <p style="color: #4b5563; font-style: italic;">
                <?php echo !empty($order['note']) ? nl2br(htmlspecialchars($order['note'])) : 'Không có ghi chú.'; ?>
            </p>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-md-4">
        <!-- Customer Info -->
        <div class="glass-panel p-4 mb-4">
            <h3 class="mb-3" style="font-size: 1.1rem; font-weight: 700;">Thông Tin Giao Hàng</h3>
            <div style="margin-bottom: 12px;">
                <div style="font-size: 0.85rem; color: #6b7280;">Người nhận:</div>
                <div style="font-weight: 600;"><?php echo htmlspecialchars($order['recipient_name']); ?></div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="font-size: 0.85rem; color: #6b7280;">Số điện thoại:</div>
                <div style="font-weight: 600;"><?php echo htmlspecialchars($order['recipient_phone']); ?></div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="font-size: 0.85rem; color: #6b7280;">Địa chỉ nhận hàng:</div>
                <div style="font-weight: 500; font-size: 0.95rem; line-height: 1.5;">
                    <?php echo htmlspecialchars($order['shipping_address']); ?>
                </div>
            </div>
            <div style="margin-bottom: 0;">
                <div style="font-size: 0.85rem; color: #6b7280;">Phương thức thanh toán:</div>
                <div style="font-weight: 600; text-transform: uppercase;"><?php echo $order['payment_method']; ?></div>
            </div>
        </div>

        <!-- Order Actions -->
        <div class="glass-panel p-4">
            <h3 class="mb-3" style="font-size: 1.1rem; font-weight: 700;">Trạng Thái Đơn Hàng</h3>
            <div class="mb-3">
                <span class="badge <?php echo $status_colors[$order['status']]; ?>" style="font-size: 0.9rem; padding: 6px 12px;">
                    <?php echo $status_labels[$order['status']]; ?>
                </span>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <div class="mb-3">
                    <label style="font-size: 0.85rem; color: #6b7280; display: block; margin-bottom: 5px;">Cập nhật trạng thái:</label>
                    <select name="new_status" class="form-control">
                        <?php foreach ($status_labels as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo ($order['status'] == $val) ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Cập Nhật</button>
            </form>
        </div>
    </div>
</div>

<style>
.row { display: flex; flex-wrap: wrap; margin-right: -12px; margin-left: -12px; }
.col-md-8 { flex: 0 0 66.666667%; max-width: 66.666667%; padding-right: 12px; padding-left: 12px; }
.col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; padding-right: 12px; padding-left: 12px; }
.mb-4 { margin-bottom: 1.5rem; }
.mb-3 { margin-bottom: 1rem; }
.w-100 { width: 100%; }
.table tfoot td { border-top: 1px solid #e5e7eb; }

@media (max-width: 768px) {
    .col-md-8, .col-md-4 { flex: 0 0 100%; max-width: 100%; }
}
</style>

<?php include 'includes/footer.php'; ?>
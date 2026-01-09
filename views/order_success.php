<?php
require_once 'controllers/OrderSuccessController.php';

if (!$order) {
    echo "<div class='container mt-2'><div class='glass-panel' style='padding: 30px;'><h3>Không tìm thấy đơn hàng!</h3><a href='index.php'>Về trang chủ</a></div></div>";
    return; // Stop rendering the rest of the view
}
?>

<link rel="stylesheet" href="assets/css/order_success.css">



<div class="container mt-2 mb-2">
    <div class="glass-panel success-container" style="padding: 50px;">

        <div class="success-icon-wrapper">
            <i class="fas fa-check success-icon"></i>
        </div>

        <h1 class="section-title" style="margin-bottom: 10px;">Đặt Hàng Thành Công!</h1>
        <p style="font-size: 1.1rem; color: #555;">Cảm ơn bạn đã tin tưởng Mâu Bakery.</p>

        <div class="order-id">
            Mã đơn hàng: <span>#<?php echo htmlspecialchars($order['order_code']); ?></span>
        </div>

        <p style="max-width: 600px; margin: 0 auto; line-height: 1.6; color: #666;">
            Chúng tôi sẽ sớm liên hệ số điện thoại
            <strong><?php echo htmlspecialchars($order['recipient_phone']); ?></strong>
            để xác nhận đơn hàng của bạn trong vòng 15 phút.
        </p>

        <!-- Order Information Grid -->
        <div class="order-info">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
                <div class="info-group">
                    <span class="info-label">Ngày đặt hàng</span>
                    <span class="info-content">
                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                    </span>
                </div>
                <div class="info-group">
                    <span class="info-label">Phương thức thanh toán</span>
                    <span class="info-content">
                        <?php
                        switch ($order['payment_method']) {
                            case 'cod':
                                echo 'Thanh toán khi nhận hàng (COD)';
                                break;
                            case 'banking':
                                echo 'Chuyển khoản ngân hàng';
                                break;
                            case 'momo':
                                echo 'Ví điện tử Momo';
                                break;
                            default:
                                echo $order['payment_method'];
                        }
                        ?>
                    </span>
                </div>
                <div class="info-group">
                    <span class="info-label">Tổng thanh toán</span>
                    <span class="info-content" style="color: var(--accent-color); font-weight: 700;">
                        <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ
                    </span>
                </div>
                <div class="info-group">
                    <span class="info-label">Địa chỉ giao hàng</span>
                    <span class="info-content"><?php echo htmlspecialchars($order['shipping_address']); ?></span>
                </div>
            </div>
        </div>

        <!-- Ordered Items Summary -->
        <div class="item-list">
            <h4 style="margin-bottom: 15px; color: var(--text-color);">Chi tiết sản phẩm</h4>
            <?php foreach ($order_items as $item): ?>
                <div class="item-row">
                    <div>
                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                        <?php if ($item['size']): ?><span
                                style="font-size: 0.9em; color: #666;">(<?php echo $item['size']; ?>)</span><?php endif; ?>
                        <br>
                        <small>x<?php echo $item['quantity']; ?></small>
                    </div>
                    <div>
                        <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="item-row"
                style="margin-top: 10px; padding-top: 10px; border-top: 2px solid #ddd; font-weight: bold;">
                <div>Phí vận chuyển</div>
                <div><?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>đ</div>
            </div>
            <div class="item-row" style="font-weight: bold; color: var(--accent-color); font-size: 1.1em;">
                <div>Tổng cộng</div>
                <div><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</div>
            </div>
        </div>

        <div class="success-actions">
            <a href="index.php" class="btn-glass btn-home">Về Trang Chủ</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=profile&tab=orders" class="btn-track">Theo Dõi Đơn Hàng</a>
            <?php else: ?>
                <a href="#" onclick="checkOrder()" class="btn-track">Theo Dõi Đơn Hàng</a>
            <?php endif; ?>
        </div>

    </div>
</div>
<script src="assets/js/order_success.js"></script>
<?php
session_start();
require_once 'controllers/OrderDetailController.php';

// No header/footer here, this is a clean print page
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #<?php echo $order['order_code']; ?> - Mâu Bakery</title>
    <style>
        body { font-family: 'Quicksand', Arial, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 40px; }
        .invoice-box { max-width: 800px; margin: auto; border: 1px solid #eee; padding: 30px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 2px solid #e91e63; padding-bottom: 20px; }
        .logo { font-size: 28px; font-weight: bold; color: #e91e63; }
        .company-info { text-align: right; font-size: 14px; }
        .invoice-title { font-size: 24px; font-weight: bold; margin-bottom: 20px; text-align: center; text-transform: uppercase; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .info-section h3 { font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 8px; margin-bottom: 12px; }
        .info-section p { margin: 4px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { background: #f8f8f8; text-align: left; padding: 12px; border-bottom: 2px solid #ddd; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .totals { float: right; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; }
        .total-row.grand-total { border-top: 2px solid #e91e63; margin-top: 10px; font-weight: bold; font-size: 18px; color: #e91e63; }
        .footer { clear: both; margin-top: 60px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 20px; }
        .print-btn { background: #e91e63; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; }
        @media print { .print-btn { display: none; } body { padding: 0; } .invoice-box { border: none; } }
    </style>
</head>
<body>
    <div style="text-align: center;">
        <button class="print-btn" onclick="window.print();"><i class="fas fa-print"></i> In Hóa Đơn Này</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo">Mâu Bakery</div>
            <div class="company-info">
                <p><strong>Địa chỉ:</strong> 123 Đường Bánh Ngọt, Quận 1, TP.HCM</p>
                <p><strong>Hotline:</strong> 0909 123 456</p>
                <p><strong>Email:</strong> mau.bakery@gmail.com</p>
            </div>
        </div>

        <h1 class="invoice-title">Hóa Đơn Bán Hàng</h1>

        <div class="info-grid">
            <div class="info-section">
                <h3>Thông tin khách hàng</h3>
                <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['recipient_name']); ?></p>
                <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['recipient_phone']); ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
            </div>
            <div class="info-section" style="text-align: right;">
                <h3>Chi tiết hóa đơn</h3>
                <p><strong>Số hóa đơn:</strong> #<?php echo $order['order_code']; ?></p>
                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                <p><strong>Thanh toán:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
                <p><strong>Trạng thái:</strong> <?php echo ($order['payment_status'] == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'); ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Kích thước</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Đơn giá</th>
                    <th style="text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($items as $item): 
                    $item_total = $item['price'] * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['size']; ?></td>
                        <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                        <td style="text-align: right;"><?php echo number_format($item_total, 0, ',', '.'); ?>đ</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Tạm tính:</span>
                <span><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</span>
            </div>
            <div class="total-row">
                <span>Phí vận chuyển:</span>
                <span>+<?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>đ</span>
            </div>
            <?php if ($order['discount_amount'] > 0): ?>
            <div class="total-row">
                <span>Giảm giá:</span>
                <span>-<?php echo number_format($order['discount_amount'], 0, ',', '.'); ?>đ</span>
            </div>
            <?php endif; ?>
            <div class="total-row grand-total">
                <span>Tổng cộng:</span>
                <span><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</span>
            </div>
        </div>

        <div class="footer">
            <p>Cảm ơn quý khách đã tin tưởng và ủng hộ Mâu Bakery!</p>
            <p>Vui lòng kiểm tra hàng trước khi nhận. Mọi thắc mắc xin liên hệ Hotline: 0909 123 456</p>
        </div>
    </div>

    <script>
        // Auto print or just let user click
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
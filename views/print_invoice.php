<?php
require_once 'controllers/OrderDetailController.php';

if (!$order) {
    echo "<h3>Đơn hàng không tồn tại hoặc bạn không có quyền truy cập!</h3>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn -
        <?php echo $order['code']; ?>
    </title>
    <link rel="stylesheet" href="assets/css/print_invoice.css">
</head>

<body>

    <div class="invoice-container">
        <!-- Print Button for Browser View -->
        <div class="no-print" style="text-align: right;">
            <button onclick="window.print()" class="btn-print">
                In Hóa Đơn
            </button>
        </div>

        <div class="invoice-header">
            <div class="company-info">
                <h1>Mâu Bakery</h1>
                <p>123 Đường Bánh Kem, Quận 1, TP.HCM</p>
                <p>Hotline: 090 123 4567</p>
                <p>Email: hello@maubakery.com</p>
                <p>Website: www.maubakery.local</p>
            </div>
            <div class="invoice-title">
                <h2>HÓA ĐƠN MUA HÀNG</h2>
                <p>Mã HĐ: <strong style="color: #4a4a4a;">#
                        <?php echo $order['code']; ?>
                    </strong></p>
                <p>Ngày:
                    <?php echo date('d/m/Y', strtotime(str_replace('/', '-', $order['date']))); ?>
                </p>
            </div>
        </div>

        <div class="billing-info">
            <div>
                <h3>Thông Tin Khách Hàng</h3>
                <p><strong>Khách hàng:</strong>
                    <?php echo $order['customer']['name']; ?>
                </p>
                <p><strong>Điện thoại:</strong>
                    <?php echo $order['customer']['phone']; ?>
                </p>
                <p><strong>Địa chỉ:</strong>
                    <?php echo $order['customer']['address']; ?>
                </p>
                <?php if ($order['customer']['note']): ?>
                    <p><strong>Ghi chú:</strong>
                        <?php echo $order['customer']['note']; ?>
                    </p>
                <?php endif; ?>
            </div>
            <div>
                <h3>Thông Tin Thanh Toán</h3>
                <p><strong>Hình thức giao:</strong>
                    <?php echo $order['shipping_method']; ?>
                </p>
                <p><strong>Phương thức TT:</strong>
                    <?php echo strtoupper($order['payment_method']); ?>
                </p>
                <p><strong>Trạng thái:</strong>
                    <?php
                    if ($order['payment_status'] == 'paid' || $order['status'] == 'completed')
                        echo 'Đã thanh toán';
                    else
                        echo 'Chưa thanh toán (Thanh toán khi nhận)';
                    ?>
                </p>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align: center;">Size</th>
                    <th style="text-align: right;">Đơn giá</th>
                    <th style="text-align: center;">SL</th>
                    <th style="text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td>
                            <strong>
                                <?php echo htmlspecialchars($item['name']); ?>
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            <?php echo $item['size'] ? htmlspecialchars($item['size']) : 'Tiêu chuẩn'; ?>
                        </td>
                        <td style="text-align: right;">
                            <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                        </td>
                        <td style="text-align: center;">
                            <?php echo $item['quantity']; ?>
                        </td>
                        <td style="text-align: right;"><strong>
                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                            </strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Tạm tính:</span>
                <span>
                    <?php echo number_format($order['subtotal'], 0, ',', '.'); ?>đ
                </span>
            </div>
            <div class="total-row">
                <span>Phí vận chuyển:</span>
                <span>
                    <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>đ
                </span>
            </div>
            <?php if ($order['discount'] > 0): ?>
                <div class="total-row">
                    <span>Voucher khuyến mãi:</span>
                    <span>-
                        <?php echo number_format($order['discount'], 0, ',', '.'); ?>đ
                    </span>
                </div>
            <?php endif; ?>

            <div class="total-row final">
                <span>Tổng Thành Tiền:</span>
                <span>
                    <?php echo number_format($order['total'], 0, ',', '.'); ?>đ
                </span>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="footer">
            <p><strong>"Vị Ngọt Hạnh Phúc"</strong></p>
            <p>Xin cảm ơn Quý khách và hẹn gặp lại!</p>
            <p style="font-style: italic; font-size: 11px; margin-top: 10px;">Hóa đơn này được tạo tự động bởi hệ thống
                cửa hàng Mâu Bakery. (In lúc:
                <?php echo date('d/m/Y H:i:s'); ?>)
            </p>
        </div>
    </div>

    <script src="assets/js/print_invoice.js"></script>

</body>

</html>
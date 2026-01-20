<?php
require_once 'controllers/CheckoutViewController.php';

// Fetch Shipping Zones from Database
$districts_data = [];
if (isset($conn)) {
    try {
        $stmt_zones = $conn->prepare("SELECT * FROM shipping_zones WHERE is_active = 1 ORDER BY fee ASC, name ASC");
        $stmt_zones->execute();
        $districts_data = $stmt_zones->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle error gracefully or log it
        $districts_data = [];
    }
}

?>

<!-- Load Checkout Assets -->
<link rel="stylesheet" href="assets/css/checkout.css">

<div class="container mt-2 mb-2">
    <div class="text-center mb-2">
        <h1 class="section-title">Thanh Toán</h1>
    </div>

    <form action="index.php?page=process_checkout" method="POST" class="checkout-layout" id="checkout-form">
        <input type="hidden" name="action" value="place_order">

        <!-- Left Column: Information Steps -->
        <div class="checkout-steps">

            <!-- Step 1: Shipping Info -->
            <div class="glass-panel" style="padding: 30px; margin-bottom: 30px;">
                <div class="step-header">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Thông Tin Giao Hàng</h3>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Họ và tên *</label>
                        <input type="text" class="form-input" name="fullname" placeholder="Nguyễn Văn A"
                            value="<?php echo htmlspecialchars($user_name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số điện thoại *</label>
                        <input type="tel" class="form-input" name="phone" placeholder="090 123 4567"
                            value="<?php echo htmlspecialchars($user_phone); ?>" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Địa chỉ email</label>
                        <input type="email" class="form-input" name="email" placeholder="example@email.com"
                            value="<?php echo htmlspecialchars($user_email); ?>">
                    </div>

                    <!-- Split Address Section -->
                    <div class="form-group">
                        <label class="form-label">Thành phố</label>
                        <input type="text" class="form-input" value="TP. Hồ Chí Minh" readonly
                            style="background: #f5f5f5; cursor: not-allowed; color: #777;">
                        <input type="hidden" name="city" value="TP. Hồ Chí Minh">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Quận / Huyện *</label>
                        <select class="form-input" name="district" id="district-select" required>
                            <option value="" data-fee="0">-- Chọn Quận/Huyện --</option>
                            <?php foreach ($districts_data as $data): ?>
                                <option value="<?php echo $data['id']; ?>" data-fee="<?php echo $data['fee']; ?>">
                                    <?php echo $data['name']; ?> - Ship:
                                    <?php echo number_format($data['fee'], 0, ',', '.'); ?>đ
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Địa chỉ cụ thể *</label>
                        <input type="text" class="form-input" name="address_specific"
                            placeholder="Số nhà, tên đường, phường/xã..."
                            value="<?php echo htmlspecialchars($user_address); ?>" required>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Ghi chú đơn hàng (Tùy chọn)</label>
                        <textarea class="form-input form-textarea" name="note"
                            placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Step 2: Payment Method (Renumbered, Shipping selection removed) -->
            <div class="glass-panel" style="padding: 30px;">
                <div class="step-header">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Phương Thức Thanh Toán</h3>
                </div>

                <!-- Hidden input for shipping fee logic if needed, though handled by district select -->
                <input type="hidden" name="shipping_fee" id="hidden_shipping_fee" value="0">


                <div class="shipping-options">
                    <label class="radio-card active">
                        <input type="radio" name="payment_method" value="cod" class="radio-input" checked>
                        <div class="radio-info">
                            <span class="radio-title"><i class="fas fa-money-bill-wave"
                                    style="color: #4CAF50; width: 25px;"></i> Thanh Toán Khi Nhận Hàng (COD)</span>
                            <span class="radio-desc">Thanh toán tiền mặt cho shipper khi nhận bánh</span>
                        </div>
                    </label>

                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="banking" class="radio-input">
                        <div class="radio-info">
                            <span class="radio-title"><i class="fas fa-university"
                                    style="color: #2196F3; width: 25px;"></i> Chuyển Khoản Ngân Hàng</span>
                            <span class="radio-desc">Vietcombank - 1234567890 - NGUYEN VAN A</span>
                        </div>
                    </label>

                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="momo" class="radio-input">
                        <div class="radio-info">
                            <span class="radio-title"><i class="fas fa-wallet" style="color: #E91E63; width: 25px;"></i>
                                Ví MoMo</span>
                            <span class="radio-desc">Quét mã QR để thanh toán</span>
                        </div>
                    </label>
                </div>
            </div>

        </div>

        <!-- Right Column: Summary -->
        <div class="checkout-summary">
            <div class="glass-panel summary-box" style="padding: 30px;">
                <h3 style="margin-bottom: 20px;">Đơn Hàng Của Bạn</h3>

                <!-- Items List -->
                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px; padding-right: 5px;">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <img src="<?php echo $item['image']; ?>" class="summary-img" alt="Product">
                            <div class="summary-details">
                                <h4 style="color: var(--text-color);">
                                    <?php echo $item['name']; ?>
                                </h4>
                                <?php if (isset($item['size'])): ?>
                                    <small><?php echo $item['size']; ?></small><br>
                                <?php endif; ?>
                                <span>
                                    <?php echo $item['quantity']; ?> x
                                    <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                                </span>
                            </div>
                            <div style="margin-left: auto; font-weight: 600;">
                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Coupon -->
                <div class="coupon-group">
                    <input type="text" placeholder="Mã giảm giá" class="form-input" style="padding: 10px;">
                    <button type="button" class="btn-apply">Áp dụng</button>
                </div>

                <!-- Totals -->
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span id="checkout-subtotal" data-amount="<?php echo $subtotal; ?>">
                        <?php echo number_format($subtotal, 0, ',', '.'); ?>đ
                    </span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span id="checkout-shipping">0đ</span>
                </div>


                <div class="summary-row total">
                    <span>Tổng cộng</span>
                    <span id="checkout-total" data-subtotal="<?php echo $subtotal; ?>">
                        <?php echo number_format($total, 0, ',', '.'); ?>đ
                    </span>
                </div>

                <!-- Privacy Check -->
                <label
                    style="display: flex; gap: 10px; font-size: 0.85rem; margin-bottom: 20px; cursor: pointer; color: #666;">
                    <input type="checkbox" required checked>
                    Tôi đồng ý với điều khoản dịch vụ và chính sách bảo mật của cửa hàng.
                </label>

                <button type="submit" class="btn-checkout-submit">
                    ĐẶT HÀNG NGAY (<?php echo count($cart_items); ?>)
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Load JS -->
<script src="assets/js/checkout.js"></script>
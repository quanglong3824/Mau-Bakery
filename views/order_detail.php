<?php
require_once 'controllers/OrderDetailController.php';
?>
if (!$order) {
    echo "<div class='container mt-2'><h3>Đơn hàng không tồn tại hoặc bạn không có quyền truy cập!</h3><a href='index.php?page=profile'>Về tài khoản</a></div>";
    return;
}

?>
<link rel="stylesheet" href="assets/css/order_detail.css">

<div class="container order-detail-container mt-4 mb-4">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 20px; font-size: 0.9rem;">
        <a href="index.php?page=profile" style="color: #6b7280; text-decoration: none;">Tài khoản</a>
        <i class="fas fa-chevron-right" style="font-size: 0.7rem; margin: 0 10px; color: #ccc;"></i>
        <span style="color: var(--accent-color); font-weight: 600;">Chi tiết đơn hàng</span>
    </div>

    <div class="glass-panel"
        style="padding: 40px; border-radius: 20px; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">

        <!-- Order Header -->
        <div class="order-header-card">
            <div>
                <h1 class="order-id-large">Đơn hàng #<?php echo $order['code']; ?></h1>
                <p class="order-meta"><i class="far fa-calendar-alt"></i> Ngày đặt: <?php echo $order['date']; ?></p>
            </div>
            <div style="text-align: right;">
                <?php

                $pay_status = $order['payment_status'];
                $pay_method = $order['payment_method'];

                if ($pay_status == 'paid' || $order['status'] == 'completed') {
                    $badge_bg = '#dcfce7'; // Green
                    $badge_color = '#166534';
                    $badge_text = 'Đã thanh toán';
                    $badge_icon = 'fa-check-circle';
                } elseif ($pay_status == 'refunded') {
                    $badge_bg = '#f3f4f6'; // Gray
                    $badge_color = '#374151';
                    $badge_text = 'Đã hoàn tiền';
                    $badge_icon = 'fa-undo';
                } else {
                    // Unpaid
                    if ($pay_method == 'cod') {
                        $badge_bg = '#dbeafe'; // Blue
                        $badge_color = '#1e40af';
                        $badge_text = 'Thanh toán khi nhận hàng';
                        $badge_icon = 'fa-hand-holding-usd';
                    } else {
                        $badge_bg = '#fffbeb'; // Yellow/Orange
                        $badge_color = '#b45309';
                        $badge_text = 'Chưa thanh toán';
                        $badge_icon = 'fa-clock';
                    }
                }
                ?>
                <span class="status-badge-large"
                    style="background: <?php echo $badge_bg; ?>; color: <?php echo $badge_color; ?>;">
                    <i class="fas <?php echo $badge_icon; ?>"></i>
                    <?php echo $badge_text; ?>
                </span>
            </div>
        </div>

        <!-- Timeline Logic -->
        <?php
        $is_online = ($order['payment_method'] == 'banking' || $order['payment_method'] == 'momo');
        $is_cod = !$is_online;

        // Progress Calculation
        $progress = 0;
        if ($order['status'] == 'pending')
            $progress = 10;
        if ($order['status'] == 'confirmed')
            $progress = 35; // Step 2
        if ($order['status'] == 'shipping')
            $progress = 65; // Step 3
        if ($order['status'] == 'completed')
            $progress = 100; // Step 4
        if ($order['status'] == 'cancelled')
            $progress = 0;

        // Define Steps
        $step1_active = true;
        $step2_active = in_array($order['status'], ['confirmed', 'shipping', 'completed']);
        $step3_active = in_array($order['status'], ['shipping', 'completed']);
        $step4_active = ($order['status'] == 'completed');

        // Labels
        $step2_label = "Đã xác nhận";
        if ($is_online && $order['payment_status'] == 'paid') {
            $step2_label = "Đã thanh toán";
        }

        $step4_label = "Hoàn thành";
        if ($is_cod) {
            $step4_label = "Thanh toán & Nhận";
        }
        ?>

        <!-- Timeline UI -->
        <div class="order-timeline">
            <div class="timeline-track"></div>
            <div class="timeline-progress" style="width: <?php echo $progress; ?>%;"></div>

            <!-- Step 1: Placed -->
            <div class="timeline-step <?php echo $step1_active ? 'completed' : ''; ?>">
                <div class="step-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="step-label">Đã đặt hàng</div>
                <div class="step-time"><?php echo $order['date']; ?></div>
            </div>

            <!-- Step 2: Confirmed / Paid -->
            <div
                class="timeline-step <?php echo $step2_active ? 'completed' : ($order['status'] == 'pending' ? 'active' : ''); ?>">
                <div class="step-icon">
                    <i class="fas <?php echo ($is_online) ? 'fa-credit-card' : 'fa-check'; ?>"></i>
                </div>
                <div class="step-label"><?php echo $step2_label; ?></div>
                <?php if ($step2_active): ?>
                    <!-- Mock time or fetch if available -->
                    <div class="step-time"><i class="fas fa-check"></i></div>
                <?php endif; ?>
            </div>

            <!-- Step 3: Shipping -->
            <div
                class="timeline-step <?php echo $step3_active ? 'completed' : ($order['status'] == 'confirmed' ? 'active' : ''); ?>">
                <div class="step-icon"><i class="fas fa-shipping-fast"></i></div>
                <div class="step-label">Đang giao</div>
            </div>

            <!-- Step 4: Completed / Paid (COD) -->
            <div
                class="timeline-step <?php echo $step4_active ? 'completed' : ($order['status'] == 'shipping' ? 'active' : ''); ?>">
                <div class="step-icon">
                    <i class="fas <?php echo ($is_cod) ? 'fa-hand-holding-usd' : 'fa-star'; ?>"></i>
                </div>
                <div class="step-label"><?php echo $step4_label; ?></div>
            </div>
        </div>

        <?php if ($order['status'] == 'cancelled'): ?>
            <div class="glass-panel"
                style="background: #FEE2E2; color: #EF4444; text-align: center; margin-bottom: 30px; font-weight: 600;">
                <i class="fas fa-times-circle"></i> Đơn hàng đã bị hủy
            </div>
        <?php endif; ?>

        <!-- Info Grid -->
        <div class="order-info-grid">
            <div class="glass-panel info-box" style="background: rgba(255,255,255,0.5); border: none;">
                <h3><i class="fas fa-map-marker-alt"></i> Địa chỉ nhận hàng</h3>
                <div class="info-content">
                    <p><strong><?php echo $order['customer']['name']; ?></strong></p>
                    <p><?php echo $order['customer']['phone']; ?></p>
                    <p style="color: #666;"><?php echo $order['customer']['address']; ?></p>
                    <?php if ($order['customer']['note']): ?>
                        <p style="margin-top: 10px; font-style: italic; color: #888;">
                            "<?php echo $order['customer']['note']; ?>"</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="glass-panel info-box" style="background: rgba(255,255,255,0.5); border: none;">
                <h3><i class="fas fa-credit-card"></i> Thanh toán & Vận chuyển</h3>
                <div class="info-content">
                    <p><span style="color: #888;">Hình thức:</span> <?php echo $order['shipping_method']; ?></p>
                    <p><span style="color: #888;">Phương thức TT:</span>
                        <?php echo ucfirst($order['payment_method']); ?></p>
                    <p>
                        <span style="color: #888;">Trạng thái TT:</span>
                        <span style="color: <?php echo $pay_color; ?>; font-weight: 600;">
                            <?php echo $pay_label; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <h3 style="margin-bottom: 20px; font-size: 1.2rem;">Sản phẩm</h3>
        <table class="order-items-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align: center;">Giá</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Tạm tính</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td>
                            <div class="item-info">
                                <img src="<?php echo $item['image']; ?>" class="item-img" alt="Product">
                                <div>
                                    <h4 style="margin-bottom: 5px; font-size: 1rem;">
                                        <?php echo $item['name']; ?>
                                    </h4>
                                    <?php if ($item['size']): ?>
                                        <span class="item-meta">Size:
                                            <?php echo $item['size']; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($order['status'] == 'completed'): ?>
                                        <div style="margin-top: 5px;">
                                            <?php if (!empty($item['is_reviewed'])): ?>
                                                <span style="font-size: 0.8rem; color: #22c55e; font-weight: 600;">
                                                    <i class="fas fa-check"></i> Đã đánh giá
                                                </span>
                                            <?php else: ?>
                                                <button
                                                    onclick="openReviewModal(<?php echo $item['product_id']; ?>, '<?php echo addslashes($item['name']); ?>')"
                                                    style="background: #fff0f3; color: var(--accent-color); border: 1px solid var(--accent-color); padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s;">
                                                    <i class="far fa-star"></i> Viết đánh giá
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                        </td>
                        <td style="text-align: center;">x
                            <?php echo $item['quantity']; ?>
                        </td>
                        <td style="text-align: right; font-weight: 600;">
                            <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Footer Summary -->
        <div class="order-footer">
            <div class="order-actions">
                <?php if ($order['status'] == 'pending'): ?>
                    <button class="btn-cancel" onclick="cancelOrder()">Hủy đơn hàng</button>
                <?php endif; ?>

                <?php if ($order['status'] == 'completed'): ?>
                    <button class="btn-glass btn-primary" style="padding: 10px 20px;">Đánh giá</button>
                    <!-- Buy again could just link to product detail of first item or similar -->
                <?php endif; ?>

                <a href="index.php?page=profile" class="btn-glass" style="padding: 10px 20px;">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="order-totals">
                <div class="total-row">
                    <span>Tạm tính</span>
                    <span>
                        <?php echo number_format($order['subtotal'], 0, ',', '.'); ?>đ
                    </span>
                </div>
                <div class="total-row">
                    <span>Phí vận chuyển</span>
                    <span>
                        <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?>đ
                    </span>
                </div>
                <?php if ($order['discount'] > 0): ?>
                    <div class="total-row">
                        <span>Giảm giá</span>
                        <span style="color: #22C55E;">-
                            <?php echo number_format($order['discount'], 0, ',', '.'); ?>đ
                        </span>
                    </div>
                <?php endif; ?>
                <div class="total-row final">
                    <span>Tổng cộng</span>
                    <span>
                        <?php echo number_format($order['total'], 0, ',', '.'); ?>đ
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="modal-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div
        style="background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 20px; position: relative; animation: slideDown 0.3s ease;">
        <h3 style="margin-bottom: 20px; font-family: 'Quicksand', sans-serif; text-align: center;">Đánh giá sản phẩm
        </h3>

        <p id="reviewProductName"
            style="text-align: center; color: var(--accent-color); font-weight: 700; margin-bottom: 20px;"></p>

        <form method="POST">
            <input type="hidden" name="action" value="submit_review">
            <input type="hidden" name="product_id" id="reviewProductId">

            <div style="text-align: center; margin-bottom: 20px;">
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" checked /><label for="star5"
                        title="5 stars">★</label>
                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4"
                        title="4 stars">★</label>
                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3"
                        title="3 stars">★</label>
                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2"
                        title="2 stars">★</label>
                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star">★</label>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600;">Nhận xét của bạn:</label>
                <textarea name="comment" rows="4"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 10px; font-family: inherit;"
                    placeholder="Chia sẻ cảm nhận của bạn về sản phẩm này..."></textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeReviewModal()" class="btn-glass"
                    style="border: none; background: #f3f4f6; cursor: pointer;">Hủy</button>
                <button type="submit" class="btn-glass btn-primary" style="border: none; cursor: pointer;">Gửi đánh
                    giá</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/order_detail.js"></script>
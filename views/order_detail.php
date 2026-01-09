<?php
// views/order_detail.php

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='auth/login.php';</script>";
    exit;
}

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if ($product_id > 0 && $rating >= 1 && $rating <= 5) {
        try {
            $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (:uid, :pid, :rating, :comment)");
            $stmt->execute([
                'uid' => $user_id,
                'pid' => $product_id,
                'rating' => $rating,
                'comment' => $comment
            ]);
            $msg_review = "Cảm ơn bạn đã đánh giá sản phẩm!";
        } catch (PDOException $e) {
            $err_review = "Lỗi: " . $e->getMessage();
        }
    }
}

// Get Order ID
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order = null;

if (isset($conn) && $order_id > 0) {
    // 1. Fetch Order Data (Security: Check user_id matches session)
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $order_id, 'user_id' => $_SESSION['user_id']]);
    $order_data = $stmt->fetch();

    if ($order_data) {
        // 2. Fetch Order Items
        $stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = :id");
        $stmt_items->execute(['id' => $order_id]);
        $items = $stmt_items->fetchAll();

        // 3. Map to View Structure
        $order = [
            'id' => $order_data['id'],
            'code' => $order_data['order_code'],
            'date' => date('d/m/Y H:i', strtotime($order_data['created_at'])),
            'status' => $order_data['status'],
            'payment_method' => $order_data['payment_method'],
            'payment_status' => $order_data['payment_status'],
            'shipping_method' => $order_data['shipping_fee'] > 30000 ? 'Giao hàng hỏa tốc' : 'Giao hàng tiêu chuẩn',
            'customer' => [
                'name' => $order_data['recipient_name'],
                'phone' => $order_data['recipient_phone'],
                'address' => $order_data['shipping_address'],
                'note' => $order_data['note']
            ],
            'items' => [],
            'subtotal' => $order_data['total_amount'] - $order_data['shipping_fee'] + $order_data['discount_amount'], // approximate reverse calc
            'shipping_fee' => $order_data['shipping_fee'],
            'discount' => $order_data['discount_amount'],
            'total' => $order_data['total_amount']
        ];

        foreach ($items as $item) {
            // We might not have image here if not joined with products. 
            // Ideally we should join. Let's do a quick fetch or join in query.
            // For now let's assume valid product_id to fetch image.
            $img = 'assets/images/default-cake.jpg'; // Fallback
            $stmt_prod = $conn->prepare("SELECT image FROM products WHERE id = :pid");
            $stmt_prod->execute(['pid' => $item['product_id']]);
            $p = $stmt_prod->fetch();
            if ($p)
                $img = $p['image'];

            // Fetch Reviews by this user for these products to check if already reviewed
            $reviewed_products = [];
            $stmt_reviews = $conn->prepare("SELECT product_id FROM reviews WHERE user_id = :uid");
            $stmt_reviews->execute(['uid' => $_SESSION['user_id']]);
            $reviewed_products = $stmt_reviews->fetchAll(PDO::FETCH_COLUMN);

            $order['items'][] = [
                'product_id' => $item['product_id'], // Need this for rating
                'name' => $item['product_name'],
                'image' => $img,
                'size' => $item['size'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'is_reviewed' => in_array($item['product_id'], $reviewed_products)
            ];
        }
    }
}

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
                    <button class="btn-cancel" onclick="alert('Tính năng hủy đang phát triển')">Hủy đơn hàng</button>
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

<style>
    @keyframes slideDown {
        from {
            transform: translateY(-30px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        gap: 5px;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        font-size: 2.5rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
        line-height: 1;
    }

    .star-rating input:checked~label,
    .star-rating label:hover,
    .star-rating label:hover~label {
        color: #ffd700;
        text-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
    }
</style>

<script>
    function openReviewModal(pid, pname) {
        document.getElementById('reviewModal').style.display = 'flex';
        document.getElementById('reviewProductId').value = pid;
        document.getElementById('reviewProductName').innerText = pname;
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
    }

    window.onclick = function (event) {
        var modal = document.getElementById('reviewModal');
        if (event.target == modal) {
            closeReviewModal();
        }
    }
</script>
<?php
require_once 'controllers/CartViewController.php';
?>

<div class="container mt-2 mb-2">
    <div class="text-center mb-2">
        <h1 class="section-title">Giỏ Hàng Của Bạn</h1>
    </div>

    <!-- State: Not Logged In Warning -->
    <?php if (!$is_logged_in): ?>
        <div class="glass-panel"
            style="padding: 15px; margin-bottom: 20px; background: rgba(255, 209, 220, 0.4); border-color: #ffb7c5; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fas fa-info-circle"></i> Bạn chưa đăng nhập. Hãy đăng nhập để lưu giỏ hàng và tích điểm thành
                viên.</span>
            <a href="auth/login.php" class="btn-glass"
                style="padding: 5px 15px; font-size: 0.9rem; background: rgba(255,255,255,0.6);">Đăng nhập ngay</a>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="glass-panel text-center" style="padding: 50px;">
            <i class="fas fa-shopping-basket" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
            <h3>Giỏ hàng của bạn đang trống</h3>
            <p>Hãy dạo một vòng thực đơn và chọn món bánh yêu thích nhé!</p>
            <a href="index.php?page=menu" class="btn-glass btn-primary mt-1">Xem Thực Đơn</a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            <!-- Cart Items List -->
            <div style="flex: 2; min-width: 300px;">
                <div class="glass-panel" style="padding: 20px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.1);">
                                <th style="text-align: left; padding-bottom: 15px;">Sản phẩm</th>
                                <th style="text-align: center; padding-bottom: 15px;">Số lượng</th>
                                <th style="text-align: right; padding-bottom: 15px;">Tạm tính</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-table-body">
                            <?php foreach ($cart_items as $index => $item): ?>
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);" data-index="<?php echo $index; ?>">
                                    <td style="padding: 20px 0; display: flex; align-items: center; gap: 15px;">
                                        <img src="<?php echo $item['image']; ?>"
                                            style="width: 80px; height: 80px; border-radius: 10px; object-fit: cover;">
                                        <div>
                                            <h4 style="margin-bottom: 5px;">
                                                <?php echo $item['name']; ?>
                                            </h4>
                                            <?php if (isset($item['size'])): ?>
                                                <small style="color: #666;">Size: <?php echo $item['size']; ?></small><br>
                                            <?php endif; ?>
                                            <span style="color: #888;">
                                                <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                                            </span>
                                        </div>
                                    </td>
                                    <td style="padding: 20px 0; text-align: center;">
                                        <div
                                            style="display: inline-flex; align-items: center; background: rgba(255,255,255,0.5); border-radius: 20px; padding: 5px;">
                                            <button
                                                onclick="updateCart(<?php echo $index; ?>, <?php echo $item['quantity'] - 1; ?>)"
                                                style="width: 30px; height: 30px; border: none; background: transparent; cursor: pointer;">-</button>

                                            <input type="number" value="<?php echo $item['quantity']; ?>"
                                                style="width: 40px; text-align: center; border: none; background: transparent; outline: none; font-weight: 600;"
                                                readonly>

                                            <button
                                                onclick="updateCart(<?php echo $index; ?>, <?php echo $item['quantity'] + 1; ?>)"
                                                style="width: 30px; height: 30px; border: none; background: transparent; cursor: pointer;">+</button>
                                        </div>
                                    </td>
                                    <td style="padding: 20px 0; text-align: right; font-weight: 600;">
                                        <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                                    </td>
                                    <td style="padding: 20px 0; text-align: right;">
                                        <button onclick="removeFromCart(<?php echo $index; ?>)"
                                            style="background: none; border: none; color: #ff6b6b; cursor: pointer;"><i
                                                class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 20px; text-align: left;">
                        <a href="index.php?page=menu" style="color: var(--accent-color); font-weight: 600;"><i
                                class="fas fa-arrow-left"></i> Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>

            <!-- Cart Summary - Changes based on state -->
            <div style="flex: 1; min-width: 250px;">
                <div class="glass-panel" style="padding: 25px;">
                    <h3 style="margin-bottom: 20px; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 10px;">Cộng
                        Giỏ Hàng</h3>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <span style="color: #666;">Tạm tính:</span>
                        <strong>
                            <?php echo number_format($subtotal, 0, ',', '.'); ?>đ
                        </strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <span style="color: #666;">Phí vận chuyển:</span>
                        <strong>
                            <?php echo number_format($shipping, 0, ',', '.'); ?>đ
                        </strong>
                    </div>

                    <!-- Logged In Only: Discount Points -->
                    <?php if ($is_logged_in): ?>
                        <!-- Temporarily hidden logic for points -->
                    <?php endif; ?>

                    <div style="border-top: 2px dashed rgba(0,0,0,0.1); margin: 20px 0;"></div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 1.2rem;">
                        <strong>Tổng cộng:</strong>
                        <strong style="color: var(--accent-color);">
                            <?php echo number_format($total, 0, ',', '.'); ?>đ
                        </strong>
                    </div>

                    <a href="index.php?page=checkout" class="btn-glass btn-primary"
                        style="width: 100%; text-align: center; border: none; display: block;">Tiến Hành Thanh Toán</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/cart.js"></script>
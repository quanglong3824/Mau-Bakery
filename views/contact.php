<?php require_once 'controllers/ContactController.php'; ?>
<div class="container mt-2 mb-2">
    <div class="text-center mb-2">
        <h1 class="section-title">Liên Hệ Với Chúng Tôi</h1>
        <p>Chúng tôi luôn lắng nghe ý kiến đóng góp của bạn để ngày càng hoàn thiện hơn.</p>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($message)): ?>
        <div
            style="background: #d4edda; color: #155724; padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; max-width: 800px; margin: 0 auto 20px auto;">
            <i class="fas fa-check-circle"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div
            style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; max-width: 800px; margin: 0 auto 20px auto;">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="contact-container">
        <!-- Contact Info -->
        <div class="contact-info-col">
            <div class="glass-panel" style="padding: 40px; height: 100%;">
                <h3 style="color: var(--accent-color); margin-bottom: 30px;">Thông Tin Liên Lạc</h3>

                <div class="contact-info-item">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Địa chỉ:</h4>
                        <p>123 Đường Bánh Kem, Quận 1, TP. Hồ Chí Minh</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Điện thoại:</h4>
                        <p>090 123 4567</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Email:</h4>
                        <p>hello@maubakery.com</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="contact-details">
                        <h4>Giờ mở cửa:</h4>
                        <p>8:00 - 22:00 (Hàng ngày)</p>
                    </div>
                </div>

                <div class="contact-social" style="margin-top: 40px;">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-col">
            <div class="glass-panel" style="padding: 40px;">
                <h3 style="margin-bottom: 30px;">Gửi Tin Nhắn</h3>
                <form action="" method="POST">

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Logged In State -->
                        <div class="logged-in-alert">
                            <div class="logged-in-alert-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="logged-in-text">
                                Bạn đang đăng nhập với tài khoản<br>
                                <strong><?php echo htmlspecialchars($contact_email); ?></strong>
                            </div>
                        </div>
                        <!-- Hidden fields to pass validation -->
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($contact_name); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($contact_email); ?>">
                    <?php else: ?>
                        <!-- Guest State -->
                        <div class="form-row-glass">
                            <div>
                                <label for="name" class="form-label">Họ tên</label>
                                <input type="text" id="name" name="name" placeholder="Nhập họ tên của bạn"
                                    class="form-control-glass" value="<?php echo htmlspecialchars($contact_name); ?>">
                            </div>
                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" placeholder="Nhập email của bạn"
                                    class="form-control-glass" value="<?php echo htmlspecialchars($contact_email); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group-glass">
                        <label for="subject" class="form-label">Tiêu đề</label>
                        <input type="text" id="subject" name="subject" placeholder="Bạn cần hỗ trợ gì?"
                            class="form-control-glass">
                    </div>

                    <div class="form-group-glass" style="margin-bottom: 30px;">
                        <label for="message" class="form-label">Nội dung</label>
                        <textarea id="message" name="message" rows="5" placeholder="Viết nội dung tin nhắn..."
                            class="form-control-glass"></textarea>
                    </div>

                    <button type="submit" name="submit_contact" class="btn-glass btn-primary"
                        style="width: 100%; border: none; padding: 15px; font-size: 1.1rem;">
                        Gửi Tin Nhắn
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
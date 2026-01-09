<?php
require_once 'controllers/FaqController.php';
?>

<div class="container mt-2 mb-2">
    <div class="text-center mb-2">
        <h1 class="section-title">Câu Hỏi Thường Gặp</h1>
        <p>Giải đáp những thắc mắc phổ biến nhất của khách hàng</p>
    </div>

    <!-- Search Box -->
    <div style="max-width: 600px; margin: 0 auto 50px;">
        <div class="glass-panel" style="padding: 10px 20px; display: flex; align-items: center; border-radius: 50px;">
            <i class="fas fa-search" style="color: #888; margin-right: 15px;"></i>
            <input type="text" id="faq-search" placeholder="Bạn cần tìm thông tin gì?"
                style="border: none; background: transparent; width: 100%; outline: none; font-size: 1rem; padding: 10px 0;">
        </div>
    </div>

    <div style="max-width: 800px; margin: 0 auto;">

        <?php if (empty($faqs_grouped)): ?>
            <div class="text-center glass-panel" style="padding: 40px;">
                <p>Hiện chưa có câu hỏi thường gặp nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($faqs_grouped as $category): ?>
                <div class="glass-panel faq-category" style="margin-bottom: 30px; padding: 30px;">
                    <h2
                        style="color: var(--accent-color); margin-bottom: 20px; font-size: 1.5rem; border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 10px;">
                        <?php echo htmlspecialchars($category['title']); ?>
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach ($category['items'] as $item): ?>
                            <details class="faq-item"
                                style="background: rgba(255,255,255,0.5); border-radius: 15px; padding: 15px; cursor: pointer; transition: all 0.3s;">
                                <summary
                                    style="font-weight: 600; list-style: none; display: flex; align-items: center; justify-content: space-between;">
                                    <span class="faq-question"><?php echo htmlspecialchars($item['q']); ?></span>
                                    <i class="fas fa-chevron-down" style="font-size: 0.8rem; color: #888;"></i>
                                </summary>
                                <p
                                    style="margin-top: 15px; color: #555; line-height: 1.6; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 10px;">
                                    <?php echo htmlspecialchars($item['a']); ?>
                                </p>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="text-center mt-2">
            <p>Vẫn chưa tìm thấy câu trả lời?</p>
            <a href="index.php?page=contact" class="btn-glass btn-primary" style="margin-top: 10px;">Liên hệ ngay</a>
        </div>

    </div>
</div>

<link rel="stylesheet" href="assets/css/faq.css">
<script src="assets/js/faq.js"></script>
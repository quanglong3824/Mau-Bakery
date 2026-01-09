<?php
require_once 'controllers/VoucherController.php';
?>

<div class="container mt-2 mb-2">
    <div class="text-center mb-2">
        <h1 class="section-title">Ưu Đãi & Khuyến Mãi</h1>
        <p>Săn deal ngọt ngào - Trao gửi yêu thương</p>
    </div>

    <!-- Vouchers Grid -->
    <h2 style="margin-bottom: 25px; font-family: 'Quicksand', sans-serif;">Mã Giảm Giá</h2>
    <div
        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-bottom: 50px;">
        <?php foreach ($vouchers as $voucher): ?>
            <div class="glass-panel" style="padding: 0; display: flex; overflow: hidden; height: 120px;">
                <div
                    style="width: 100px; background: <?php echo $voucher['color']; ?>; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #4a4a4a;">
                    <span
                        style="font-weight: 800; font-size: 1.2rem; writing-mode: vertical-rl; text-orientation: mixed; transform: rotate(180deg);">VOUCHER</span>
                </div>
                <div
                    style="flex: 1; padding: 15px; display: flex; flex-direction: column; justify-content: center; border-left: 2px dashed rgba(255,255,255,0.8);">
                    <div
                        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px;">
                        <span style="font-weight: 700; font-size: 1.2rem; color: var(--accent-color);">
                            <?php echo $voucher['discount']; ?>
                        </span>
                        <button class="btn-copy" onclick="copyVoucher('<?php echo $voucher['code']; ?>')"
                            style="border: 1px solid var(--accent-color); background: white; color: var(--accent-color); border-radius: 5px; cursor: pointer; font-size: 0.8rem; padding: 2px 8px;">Copy</button>
                    </div>
                    <p style="font-size: 0.9rem; margin-bottom: 5px;">
                        <?php echo $voucher['desc']; ?>
                    </p>
                    <div style="font-size: 0.8rem; color: #888;">
                        <p>Đơn tối thiểu:
                            <?php echo $voucher['min_spend']; ?>
                        </p>
                        <p>HSD:
                            <?php echo $voucher['expiry']; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Promotions List -->
    <h2 style="margin-bottom: 25px; font-family: 'Quicksand', sans-serif;">Chương Trình Nổi Bật</h2>
    <div style="display: flex; flex-direction: column; gap: 30px;">
        <?php foreach ($promotions as $promo): ?>
            <div class="glass-panel promo-panel" style="padding: 0; display: flex; overflow: hidden;  min-height: 250px;">
                <div style="flex: 1; padding: 40px; display: flex; flex-direction: column; justify-content: center;">
                    <span
                        style="display: inline-block; padding: 5px 15px; background: #FFD1DC; color: #d63384; font-weight: 700; border-radius: 20px; width: fit-content; margin-bottom: 15px;">HOT
                        DEAL</span>
                    <h3 style="font-size: 2rem; margin-bottom: 15px; font-family: 'Quicksand', sans-serif;">
                        <?php echo $promo['title']; ?>
                    </h3>
                    <p style="font-size: 1.1rem; color: #666; margin-bottom: 25px;">
                        <?php echo $promo['desc']; ?>
                    </p>
                    <a href="<?php echo $promo['link']; ?>" class="btn-glass btn-primary" style="width: fit-content;">Xem
                        chi tiết</a>
                </div>
                <div style="flex: 1; position: relative;">
                    <img src="<?php echo $promo['image']; ?>"
                        style="width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0;">
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<link rel="stylesheet" href="assets/css/vouchers.css">
<script src="assets/js/vouchers.js"></script>
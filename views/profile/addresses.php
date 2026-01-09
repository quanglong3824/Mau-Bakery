<?php
// views/profile/addresses.php
?>
<div id="addresses">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="content-title" style="margin-bottom: 0; border: none;">Sổ Địa Chỉ</h2>
        <button class="btn-glass" style="font-size: 0.9rem;"
            onclick="document.getElementById('addAddressModal').style.display='flex'">
            <i class="fas fa-plus"></i> Thêm địa chỉ mới
        </button>
    </div>

    <div class="address-grid">
        <?php if (empty($addresses)): ?>
            <p style="color: #666;">Chưa có địa chỉ nào được lưu.</p>
        <?php else: ?>
            <?php foreach ($addresses as $addr): ?>
                <div class="address-card <?php echo $addr['is_default'] ? 'default' : ''; ?>">
                    <?php if ($addr['is_default']): ?>
                        <span class="default-badge">Mặc định</span>
                    <?php endif; ?>

                    <h4 style="margin-bottom: 10px;">
                        <?php echo htmlspecialchars($addr['recipient_name']); ?>
                    </h4>
                    <p style="color: #666; font-size: 0.95rem; margin-bottom: 5px;">
                        <?php echo htmlspecialchars($addr['address']); ?>
                    </p>
                    <p style="color: #666; font-size: 0.95rem;">SĐT:
                        <?php echo htmlspecialchars($addr['phone']); ?>
                    </p>

                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button
                            style="color: var(--accent-color); border: none; background: none; cursor: pointer; font-weight: 600;">Sửa</button>
                        <?php if (!$addr['is_default']): ?>
                            <button style="color: #ff6b6b; border: none; background: none; cursor: pointer;">Xóa</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add Address Modal -->
<div id="addAddressModal" class="modal-overlay" onclick="if(event.target === this) this.style.display='none'">
    <div class="modal-content">
        <h3 style="margin-bottom: 20px; font-family: 'Quicksand', sans-serif;">Thêm Địa Chỉ Mới</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_address">
            <div class="form-group">
                <label class="form-label">Tên người nhận</label>
                <input type="text" name="recipient_name" class="form-input" required placeholder="Ví dụ: Nguyễn Văn A">
            </div>
            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" name="phone" class="form-input" required placeholder="Ví dụ: 0987123456">
            </div>
            <div class="form-group">
                <label class="form-label">Địa chỉ chi tiết</label>
                <textarea name="address" class="form-input" rows="3" required
                    placeholder="Số nhà, Tên đường, Phường/Xã..."></textarea>
            </div>
            <div class="form-group">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="is_default"> Đặt làm mặc định
                </label>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn-glass"
                    onclick="document.getElementById('addAddressModal').style.display='none'"
                    style="margin-right: 10px;">Hủy</button>
                <button type="submit" class="btn-save">Lưu Địa Chỉ</button>
            </div>
        </form>
    </div>
</div>
<?php
session_start();
require_once 'controllers/VoucherController.php';
include 'includes/header.php';
?>

<div class="header-bar">
    <h1 class="page-title">Quản Lý Mã Giảm Giá (Voucher)</h1>
    <button class="btn btn-primary" onclick="document.getElementById('addVoucherModal').style.display='flex'">
        <i class="fas fa-plus"></i> Thêm Mã Mới
    </button>
</div>

<?php if (isset($msg)): ?>
    <div class="alert-custom-green mb-4">
        <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="glass-panel p-0 overflow-hidden">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Mã Code</th>
                <th>Giảm giá</th>
                <th>Đơn tối thiểu</th>
                <th>Số lượng</th>
                <th>Hết hạn</th>
                <th>Trạng thái</th>
                <th style="text-align: center;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vouchers as $v): ?>
            <tr>
                <td class="font-bold"><?php echo $v['code']; ?></td>
                <td>
                    <?php 
                        if ($v['discount_type'] == 'percent') {
                            echo $v['discount_value'] . '%';
                        } else {
                            echo number_format($v['discount_value'], 0, ',', '.') . 'đ';
                        }
                    ?>
                </td>
                <td><?php echo number_format($v['min_order'], 0, ',', '.'); ?>đ</td>
                <td><?php echo $v['quantity']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($v['expiry_date'])); ?></td>
                <td>
                    <span class="badge <?php echo $v['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo $v['is_active'] ? 'Đang chạy' : 'Đang tạm dừng'; ?>
                    </span>
                </td>
                <td style="text-align: center;">
                    <a href="?toggle=<?php echo $v['id']; ?>" class="btn btn-sm <?php echo $v['is_active'] ? 'btn-warning' : 'btn-success'; ?>" title="Bật/Tắt">
                        <i class="fas <?php echo $v['is_active'] ? 'fa-pause' : 'fa-play'; ?>"></i>
                    </a>
                    <a href="?delete=<?php echo $v['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa mã này?')" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php echo render_pagination($current_page, $total_pages, 'vouchers.php?'); ?>

<!-- Add Voucher Modal -->
<div id="addVoucherModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 20px;">
        <h3 class="mb-4">Thêm Mã Giảm Giá Mới</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label>Mã Code</label>
                <input type="text" name="code" class="form-control" placeholder="VÍ DỤ: GIAMGIA10" required>
            </div>
            <div class="row mb-3" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Loại giảm giá</label>
                    <select name="discount_type" class="form-control">
                        <option value="fixed">Số tiền cố định (VNĐ)</option>
                        <option value="percent">Phần trăm (%)</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>Giá trị</label>
                    <input type="number" name="discount_value" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label>Đơn tối thiểu (VNĐ)</label>
                <input type="number" name="min_order" class="form-control" value="0">
            </div>
            <div class="row mb-3" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Số lượng</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
                <div style="flex: 1;">
                    <label>Ngày hết hạn</label>
                    <input type="date" name="expiry_date" class="form-control" required>
                </div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addVoucherModal').style.display='none'">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu Mã</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
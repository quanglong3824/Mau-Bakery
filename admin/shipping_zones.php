<?php
session_start();
require_once 'controllers/ShippingZoneController.php';
include 'includes/header.php';
?>

<div class="header-bar">
    <h1 class="page-title">Quản lý Khu vực Ship</h1>
    <button class="btn btn-primary" onclick="document.getElementById('addZoneModal').style.display='flex'">
        <i class="fas fa-plus"></i> Thêm Khu Vực
    </button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Thao tác thành công!</div>
<?php endif; ?>

<div class="glass-panel p-0 overflow-hidden">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Khu vực / Quận Huyện</th>
                <th>Phí Ship (VNĐ)</th>
                <th>Trạng thái</th>
                <th style="text-align: center;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($zones as $zone): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: var(--accent-color);">
                            <?php echo htmlspecialchars($zone['name']); ?>
                        </div>
                    </td>
                    <td>
                        <?php echo number_format($zone['fee'], 0, ',', '.'); ?> đ
                    </td>
                    <td>
                        <a href="shipping_zones.php?action=toggle&id=<?php echo $zone['id']; ?>" class="badge <?php echo $zone['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo $zone['is_active'] ? 'Hoạt động' : 'Tạm ẩn'; ?>
                        </a>
                    </td>
                    <td style="text-align: center;">
                        <button class="btn btn-sm btn-info text-white" onclick='openEditModal(<?php echo json_encode($zone); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="shipping_zones.php?action=delete&id=<?php echo $zone['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php echo render_pagination($current_page, $total_pages, 'shipping_zones.php?'); ?>

<!-- ADD MODAL -->
<div id="addZoneModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel" style="background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 20px;">
        <h3 class="mb-4">Thêm Khu Vực Mới</h3>
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Tên Quận/Huyện</label>
                <input type="text" name="name" class="form-control" placeholder="Ví dụ: Quận 1, TP. Thủ Đức..." required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phí Ship (VNĐ)</label>
                <input type="number" name="fee" class="form-control" placeholder="0" required>
            </div>
            <div class="mb-4">
                <label>
                    <input type="checkbox" name="is_active" checked> Kích hoạt ngay
                </label>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addZoneModal').style.display='none'">Hủy</button>
                <button type="submit" name="add_zone" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editZoneModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel" style="background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 20px;">
        <h3 class="mb-4">Cập nhật Khu Vực</h3>
        <form action="" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
                <label class="form-label">Tên Quận/Huyện</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Phí Ship (VNĐ)</label>
                <input type="number" name="fee" id="edit_fee" class="form-control" required>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editZoneModal').style.display='none'">Hủy</button>
                <button type="submit" name="edit_zone" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/shipping_zones.js"></script>

<?php include 'includes/footer.php'; ?>
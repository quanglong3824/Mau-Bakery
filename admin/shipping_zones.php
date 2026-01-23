<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'includes/auth_check.php';
require_once 'controllers/ShippingZoneController.php';

$controller = new ShippingZoneController($conn);
$controller->handleRequest();
$zones = $controller->getZones();
?>

<!-- Module CSS -->
<link rel="stylesheet" href="assets/css/shipping_zones.css">

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h2 class="page-title">Quản lý Khu vực Ship (Shipping Zones)</h2>
    <a href="#" class="btn-add" onclick="openAddModal()">
        <i class="fas fa-plus"></i> Thêm Khu Vực
    </a>
</div>

<!-- Zones Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Khu vực / Quận Huyện</th>
                <th>Phí Ship (VNĐ)</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
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
                        <a href="shipping_zones.php?action=toggle&id=<?php echo $zone['id']; ?>"
                            class="status-badge <?php echo $zone['is_active'] ? 'active' : 'inactive'; ?>" style="padding: 5px 10px; border-radius: 20px; text-decoration: none; font-size: 0.85rem; 
                                   background: <?php echo $zone['is_active'] ? 'rgba(46, 204, 113, 0.15)' : 'rgba(231, 76, 60, 0.15)'; ?>; 
                                   color: <?php echo $zone['is_active'] ? '#2ecc71' : '#e74c3c'; ?>;">
                            <?php echo $zone['is_active'] ? 'Hoạt động' : 'Tạm ẩn'; ?>
                        </a>
                    </td>
                    <td>
                        <button class="action-btn btn-edit" onclick='openEditModal(<?php echo json_encode($zone); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn btn-delete" onclick="confirmDelete(<?php echo $zone['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</main>

<!-- ADD MODAL -->
<div id="addZoneModal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('addZoneModal')">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--accent-color);">Thêm Khu Vực Mới</h3>

        <form action="" method="POST">
            <div class="form-group">
                <label>Tên Quận/Huyện</label>
                <input type="text" name="name" class="form-control" placeholder="Ví dụ: Quận 1, TP. Thủ Đức..."
                    required>
            </div>

            <div class="form-group">
                <label>Phí Ship (VNĐ)</label>
                <input type="number" name="fee" class="form-control" placeholder="0" required>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" checked> Kích hoạt ngay
                </label>
            </div>

            <button type="submit" name="add_zone" class="btn-add" style="width: 100%; justify-content: center;">
                Lưu
            </button>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editZoneModal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('editZoneModal')">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--accent-color);">Cập nhật Khu Vực</h3>

        <form action="" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="form-group">
                <label>Tên Quận/Huyện</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Phí Ship (VNĐ)</label>
                <input type="number" name="fee" id="edit_fee" class="form-control" required>
            </div>

            <button type="submit" name="edit_zone" class="btn-add" style="width: 100%; justify-content: center;">
                Cập nhật
            </button>
        </form>
    </div>
</div>

<script src="assets/js/shipping_zones.js"></script>

</body>

</html>
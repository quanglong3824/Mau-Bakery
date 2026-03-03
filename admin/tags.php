<?php
session_start();
require_once 'controllers/TagManagerController.php';
include 'includes/header.php';
?>

<div class="header-bar">
    <h1 class="page-title">Gợi ý Hôm Nay (Tags)</h1>
    <button class="btn btn-primary" onclick="document.getElementById('addTagModal').style.display='flex'">
        <i class="fas fa-plus"></i> Thêm Tag Mới
    </button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Thao tác thành công!</div>
<?php endif; ?>

<div class="glass-panel p-0 overflow-hidden">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Thứ tự</th>
                <th>Tên Tag / Hiển thị</th>
                <th>Icon</th>
                <th>Đường dẫn tích hợp</th>
                <th>Trạng thái</th>
                <th style="text-align: center;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tags as $tag): ?>
                <tr>
                    <td><?php echo $tag['sort_order']; ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
                            <?php if ($tag['icon']): ?><i class="<?php echo $tag['icon']; ?>"></i><?php endif; ?>
                            <?php echo htmlspecialchars($tag['name']); ?>
                        </div>
                    </td>
                    <td><small><?php echo $tag['icon'] ?: '-'; ?></small></td>
                    <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.85rem; color: #6b7280;">
                        <?php echo $tag['url']; ?>
                    </td>
                    <td>
                        <a href="tags.php?action=toggle&id=<?php echo $tag['id']; ?>" class="badge <?php echo $tag['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo $tag['is_active'] ? 'Hiển thị' : 'Đã ẩn'; ?>
                        </a>
                    </td>
                    <td style="text-align: center;">
                        <button class="btn btn-sm btn-info text-white" onclick='openEditModal(<?php echo json_encode($tag); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="tags.php?action=delete&id=<?php echo $tag['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php echo render_pagination($current_page, $total_pages, 'tags.php?'); ?>

<!-- ADD MODAL -->
<div id="addTagModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel" style="background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 20px;">
        <h3 class="mb-4">Thêm Tag Gợi Ý</h3>
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Tên Tag (Hiển thị trên nút)</label>
                <input type="text" name="name" class="form-control" placeholder="Ví dụ: Ít ngọt, Healthy..." required>
            </div>
            <div class="mb-3">
                <label class="form-label">Icon (FontAwesome Class)</label>
                <input type="text" name="icon" class="form-control" placeholder="Ví dụ: fas fa-heart">
            </div>
            <div style="background: #f9fafb; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                <label class="form-label">Cấu hình Đường dẫn</label>
                <select id="add_linkType" class="form-control mb-2">
                    <option value="custom">Tự nhập Link thủ công</option>
                    <option value="category">Liên kết đến Danh mục Bánh</option>
                    <option value="product_collection">Sản phẩm cụ thể</option>
                    <option value="search">Từ khóa tìm kiếm</option>
                </select>
                <div id="add_specificSelect">
                    <input type="text" class="form-control" placeholder="Nhập đường dẫn tùy ý..." onchange="document.getElementById('add_url').value = this.value">
                </div>
                <input type="hidden" name="url" id="add_url" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Thứ tự hiển thị</label>
                <input type="number" name="sort_order" class="form-control" value="0">
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addTagModal').style.display='none'">Hủy</button>
                <button type="submit" name="add_tag" class="btn btn-primary">Lưu Tag</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editTagModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel" style="background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 20px;">
        <h3 class="mb-4">Cập nhật Tag</h3>
        <form action="" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
                <label class="form-label">Tên Tag</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Icon</label>
                <input type="text" name="icon" id="edit_icon" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Đường dẫn (URL)</label>
                <input type="text" name="url" id="edit_url" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Thứ tự</label>
                <input type="number" name="sort_order" id="edit_sort_order" class="form-control">
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editTagModal').style.display='none'">Hủy</button>
                <button type="submit" name="edit_tag" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    const categoryData = <?php echo json_encode($categories); ?>;
    const productData = <?php echo json_encode($products); ?>;
</script>
<script src="assets/js/tags.js"></script>

<?php include 'includes/footer.php'; ?>
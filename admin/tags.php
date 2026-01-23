<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'includes/auth_check.php';
require_once 'controllers/TagManagerController.php';

$controller = new TagManagerController($conn);
$controller->handleRequest();
$tags = $controller->getTags();
$categories = $controller->getCategories();
$products = $controller->getProducts();
?>

<!-- Module CSS -->
<link rel="stylesheet" href="assets/css/tags.css">

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h2 class="page-title">Gợi ý Hôm Nay (Tags)</h2>
    <a href="#" class="btn-add">
        <i class="fas fa-plus"></i> Thêm Tag Mới
    </a>
</div>

<!-- Tags Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Thứ tự</th>
                <th>Tên Tag / Hiển thị</th>
                <th>Icon Class</th>
                <th>Đường dẫn tích hợp (Link)</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tags as $tag): ?>
                <tr>
                    <td>
                        <?php echo $tag['sort_order']; ?>
                    </td>
                    <td>
                        <div class="tag-preview">
                            <?php if ($tag['icon']): ?><i class="<?php echo $tag['icon']; ?>"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($tag['name']); ?>
                        </div>
                    </td>
                    <td><small>
                            <?php echo $tag['icon'] ?: '-'; ?>
                        </small></td>
                    <td style="max-width: 250px; word-wrap: break-word; font-size: 0.85rem; color: #666;">
                        <?php echo $tag['url']; ?>
                    </td>
                    <td>
                        <a href="tags.php?action=toggle&id=<?php echo $tag['id']; ?>"
                            class="status-badge <?php echo $tag['is_active'] ? 'active' : 'inactive'; ?>">
                            <?php echo $tag['is_active'] ? 'Hiển thị' : 'Đã ẩn'; ?>
                        </a>
                    </td>
                    <td>
                        <button class="action-btn btn-edit" onclick='openEditModal(<?php echo json_encode($tag); ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn btn-delete" onclick="confirmDelete(<?php echo $tag['id']; ?>)">
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
<div id="addTagModal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--accent-color);">Thêm Tag Gợi Ý</h3>

        <form action="" method="POST">
            <div class="form-group">
                <label>Tên Tag (Hiển thị trên nút)</label>
                <input type="text" name="name" class="form-control" placeholder="Ví dụ: Ít ngọt, Healthy..." required>
            </div>

            <div class="form-group">
                <label>Icon (FontAwesome Class - Tùy chọn)</label>
                <input type="text" name="icon" class="form-control" placeholder="Ví dụ: fas fa-heart">
            </div>

            <!-- Smart Link Builder -->
            <div style="background: #f9f9f9; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <label style="font-weight: 600;">Cấu hình Đường dẫn</label>
                <div class="form-group" style="margin-bottom: 10px;">
                    <select id="add_linkType" class="form-control">
                        <option value="custom">Tự nhập Link thủ công</option>
                        <option value="category">Liên kết đến Danh mục Bánh</option>
                        <option value="product_collection">Sản phẩm (Chọn 1 hoặc nhiều)</option>
                        <option value="search">Liên kết đến Kết quả tìm kiếm (Từ khóa)</option>
                    </select>
                </div>
                <div id="add_specificSelect" class="form-group">
                    <!-- Dynamic Content filled by JS -->
                    <input type="text" class="form-control" placeholder="Nhập đường dẫn tùy ý..."
                        onchange="document.getElementById('add_url').value = this.value">
                </div>
                <input type="hidden" name="url" id="add_url" required>
            </div>

            <div class="form-group">
                <label>Thứ tự hiển thị</label>
                <input type="number" name="sort_order" class="form-control" value="0">
            </div>

            <button type="submit" name="add_tag" class="btn-add" style="width: 100%; justify-content: center;">Lưu
                Tag Mới</button>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editTagModal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--accent-color);">Cập nhật Tag</h3>

        <form action="" method="POST">
            <input type="hidden" name="id" id="edit_id">

            <div class="form-group">
                <label>Tên Tag</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Icon</label>
                <input type="text" name="icon" id="edit_icon" class="form-control">
            </div>

            <!-- Smart Link Builder (Simplified for Edit - just show raw URL for now allowing override) -->
            <div style="background: #f9f9f9; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <label style="font-weight: 600;">Đường dẫn (URL)</label>
                <input type="text" name="url" id="edit_url" class="form-control" required>
                <small style="color: #666; display: block; margin-top: 5px;">Bạn có thể tạo lại link ở trên nếu muốn
                    thay đổi.</small>

                <div class="form-group" style="margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 10px;">
                    <label>Hoặc chọn lại loại liên kết:</label>
                    <select id="edit_linkType" class="form-control">
                        <option value="">-- Giữ nguyên --</option>
                        <option value="custom">Tự nhập</option>
                        <option value="search">Tìm kiếm</option>
                        <option value="product_collection">Sản phẩm (Chọn 1 hoặc nhiều)</option>
                    </select>
                    <div id="edit_specificSelect" style="margin-top: 10px;"></div>
                </div>
            </div>

            <div class="form-group">
                <label>Thứ tự</label>
                <input type="number" name="sort_order" id="edit_sort_order" class="form-control">
            </div>

            <button type="submit" name="edit_tag" class="btn-add" style="width: 100%; justify-content: center;">Cập
                nhật</button>
        </form>
    </div>
</div>

<!-- Pass PHP Data to JS -->
<script>
    const categoryData = <?php echo json_encode($categories); ?>;
    const productData = <?php echo json_encode($products); ?>;
</script>
<script src="assets/js/tags.js"></script>

</body>

</html>
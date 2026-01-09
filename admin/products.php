<?php
session_start();
// Include Controller
require_once 'controllers/ProductManagerController.php';

require_once 'includes/header.php';
?>

<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-box"></i> Quản Lý Sản Phẩm</h2>
        <button class="btn btn-primary btn-glass" onclick="openModal('add')">
            <i class="fas fa-plus"></i> Thêm Sản Phẩm
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Product Table -->
    <div class="glass-panel p-0 overflow-hidden">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td>#
                            <?php echo $p['id']; ?>
                        </td>
                        <td>
                            <img src="<?php echo $p['image']; ?>" alt="img"
                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                        </td>
                        <td style="font-weight: 600;">
                            <?php echo htmlspecialchars($p['name']); ?>
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(177, 156, 217, 0.2); color: var(--accent-color);">
                                <?php echo $p['category_name'] ?? 'Chưa phân loại'; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo number_format($p['base_price'], 0, ',', '.'); ?>đ
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info text-white"
                                onclick='openModal("edit", <?php echo json_encode($p); ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline-block;"
                                onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-3 d-flex justify-content-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?p=<?php echo $i; ?>" class="btn btn-sm <?php echo $i == $page ? 'btn-primary' : 'btn-light'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Modal -->
<div id="productModal" class="modal-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel"
        style="background: white; width: 90%; max-width: 600px; padding: 30px; position: relative;">
        <h3 id="modalTitle" class="mb-4">Thêm Sản Phẩm</h3>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="productId">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" id="pName" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" id="pCat" class="form-control">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Giá (VNĐ)</label>
                <input type="number" name="price" id="pPrice" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Hình Ảnh</label>
                <input type="file" name="image_file" class="form-control mb-2">
                <input type="text" name="image" id="pImage" class="form-control"
                    placeholder="Hoặc nhập Link ảnh (https://...)">
                <button type="button" class="btn btn-sm btn-info text-white mt-2"
                    onclick="openLibrarySelector('pImage')">
                    <i class="fas fa-images"></i> Chọn từ Thư Viện
                </button>
                <small class="text-muted d-block mt-1">Chọn file để upload hoặc lấy từ thư viện.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" id="pDesc" class="form-control" rows="3"></textarea>
            </div>

            <div class="text-end">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Library Selector Modal (Reusable) -->
<div id="libModal" class="modal-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 2000; justify-content: center; align-items: center;">
    <div class="glass-panel"
        style="background: white; width: 90%; max-width: 800px; height: 80vh; padding: 20px; display: flex; flex-direction: column;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Chọn Ảnh Từ Thư Viện</h3>
            <button onclick="document.getElementById('libModal').style.display='none'"
                class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></button>
        </div>
        <div id="libGrid"
            style="flex: 1; overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; padding: 10px; background: #f9fafb; border-radius: 8px;">
            <!-- Loaded via JS -->
            <p style="grid-column: 1/-1; text-align: center;">Đang tải...</p>
        </div>
    </div>
</div>

<link rel="stylesheet" href="assets/css/products.css">

<script src="assets/js/products.js"></script>

<?php require_once 'includes/footer.php'; ?>
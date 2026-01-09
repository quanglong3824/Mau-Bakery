<?php
session_start();
// Include Controller
require_once 'controllers/CategoryController.php';

$page_title = "Quản Lý Danh Mục";
require_once 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/categories.css">

<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tags"></i> Quản Lý Danh Mục</h2>
        <button class="btn btn-primary btn-glass" onclick="openModal('add')">
            <i class="fas fa-plus"></i> Thêm Danh Mục
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="glass-panel p-0 overflow-hidden">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>#<?php echo $cat['id']; ?></td>
                        <td>
                            <?php if ($cat['image']): ?>
                                <img src="<?php echo $cat['image']; ?>" alt="img"
                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 600;"><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td><?php echo $cat['slug']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info text-white"
                                onclick='openModal("edit", <?php echo json_encode($cat); ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline-block;"
                                onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
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
</div>

<!-- Modal -->
<div id="catModal" class="modal-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel" style="background: white; width: 90%; max-width: 500px; padding: 30px;">
        <h3 id="modalTitle" class="mb-4">Thêm Danh Mục</h3>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="catId">

            <div class="mb-3">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="name" id="catName" class="form-control" required oninput="generateSlug()">
            </div>

            <div class="mb-3">
                <label class="form-label">Slug (URL)</label>
                <input type="text" name="slug" id="catSlug" class="form-control" placeholder="tu-dong-tao">
            </div>

            <div class="mb-3">
                <label class="form-label">Hình Ảnh</label>
                <input type="file" name="image_file" class="form-control mb-2">
                <input type="text" name="image" id="catImage" class="form-control"
                    placeholder="Hoặc nhập Link ảnh (https://...)">
                <small class="text-muted">Chọn file để upload hoặc nhập link ảnh trực tiếp.</small>
            </div>

            <div class="text-end">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/categories.js"></script>

<?php require_once 'includes/footer.php'; ?>
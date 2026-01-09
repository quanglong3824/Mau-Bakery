<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

$page_title = "Quản Lý Danh Mục";
require_once 'includes/header.php';

$message = "";
$error = "";

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    // 1. Delete
    if ($_POST['action'] == 'delete') {
        $id = intval($_POST['id']);
        try {
            $stmt = $conn->prepare("UPDATE categories SET is_active = 0 WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $message = "Đã ẩn danh mục thành công!";
        } catch (PDOException $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }

    // 2. Add/Edit
    if ($_POST['action'] == 'add' || $_POST['action'] == 'edit') {
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $image = trim($_POST['image']); // Default to text input or current image

        // Handle File Upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
            $upload_dir = __DIR__ . '/../uploads/'; // Absolute path
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (!is_writable($upload_dir)) {
                chmod($upload_dir, 0777);
            }

            $file_name = time() . '_cat_' . basename($_FILES['image_file']['name']);
            $target_file = $upload_dir . $file_name;
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Allow certain file formats
            $allowed_types = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
            if (in_array($image_file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
                    $image = 'uploads/' . $file_name; // Save relative path
                } else {
                    $error = "Không thể lưu file ảnh vào uploads.";
                }
            } else {
                $error = "Chỉ chấp nhận các định dạng JPG, JPEG, PNG, GIF, WEBP.";
            }
        }

        // Simple slug generation if empty
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        if (empty($name)) {
            $error = "Tên danh mục không được để trống.";
        } else {
            try {
                if ($_POST['action'] == 'add') {
                    $stmt = $conn->prepare("INSERT INTO categories (name, slug, image, is_active) VALUES (:name, :slug, :img, 1)");
                    $stmt->execute(['name' => $name, 'slug' => $slug, 'img' => $image]);
                    $message = "Đã thêm danh mục mới!";
                } else {
                    $id = intval($_POST['id']);
                    $stmt = $conn->prepare("UPDATE categories SET name = :name, slug = :slug, image = :img WHERE id = :id");
                    $stmt->execute(['name' => $name, 'slug' => $slug, 'img' => $image, 'id' => $id]);
                    $message = "Đã cập nhật danh mục!";
                }
            } catch (PDOException $e) {
                $error = "Lỗi: " . $e->getMessage();
            }
        }
    }
}

// Fetch Categories
$categories = $conn->query("SELECT * FROM categories WHERE is_active = 1")->fetchAll();
?>

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

<script>
    function openModal(mode, data = null) {
        document.getElementById('catModal').style.display = 'flex';
        if (mode === 'add') {
            document.getElementById('modalTitle').innerText = 'Thêm Danh Mục';
            document.getElementById('formAction').value = 'add';
            document.getElementById('catId').value = '';
            document.getElementById('catName').value = '';
            document.getElementById('catSlug').value = '';
            document.getElementById('catImage').value = '';
        } else {
            document.getElementById('modalTitle').innerText = 'Cập Nhật Danh Mục';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('catId').value = data.id;
            document.getElementById('catName').value = data.name;
            document.getElementById('catSlug').value = data.slug;
            document.getElementById('catImage').value = data.image;
        }
    }

    function closeModal() {
        document.getElementById('catModal').style.display = 'none';
    }

    function generateSlug() {
        let name = document.getElementById('catName').value;
        let slug = name.toLowerCase()
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
            .replace(/[đĐ]/g, "d")
            .replace(/[^a-z0-9\s-]/g, '')
            .trim().replace(/\s+/g, '-');
        document.getElementById('catSlug').value = slug;
    }

    window.onclick = function (event) {
        if (event.target == document.getElementById('catModal')) {
            closeModal();
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>
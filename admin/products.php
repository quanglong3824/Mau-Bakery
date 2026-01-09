<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

$page_title = "Quản Lý Sản Phẩm";
require_once 'includes/header.php';

// Handle Actions
$message = "";
$error = "";

// 1. Delete Product
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = intval($_POST['id']);
    try {
        $stmt = $conn->prepare("UPDATE products SET is_active = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $message = "Đã xóa sản phẩm thành công!";
    } catch (PDOException $e) {
        $error = "Lỗi xóa sản phẩm: " . $e->getMessage();
    }
}

// 2. Add/Edit Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && ($_POST['action'] == 'add' || $_POST['action'] == 'edit')) {
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
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

        $file_name = time() . '_' . basename($_FILES['image_file']['name']);
        $target_file = $upload_dir . $file_name;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allow certain file formats
        $allowed_types = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
        if (in_array($image_file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
                $image = 'uploads/' . $file_name; // Save relative path
            } else {
                $error = "Không thể lưu file ảnh vào thư mục uploads.";
            }
        } else {
            $error = "Chỉ chấp nhận các định dạng JPG, JPEG, PNG, GIF, WEBP.";
        }
    }

    if (empty($error)) {
        if (empty($name) || $price <= 0) {
            $error = "Vui lòng nhập tên và giá hợp lệ.";
        } else {
            try {
                if ($_POST['action'] == 'add') {
                    // Add
                    $stmt = $conn->prepare("INSERT INTO products (name, category_id, base_price, description, image, is_active) VALUES (:name, :cat, :price, :desc, :img, 1)");
                    $stmt->execute([
                        'name' => $name,
                        'cat' => $category_id,
                        'price' => $price,
                        'desc' => $description,
                        'img' => $image
                    ]);
                    $message = "Đã thêm sản phẩm mới!";
                } else {
                    // Edit
                    $id = intval($_POST['id']);
                    $stmt = $conn->prepare("UPDATE products SET name = :name, category_id = :cat, base_price = :price, description = :desc, image = :img WHERE id = :id");
                    $stmt->execute([
                        'name' => $name,
                        'cat' => $category_id,
                        'price' => $price,
                        'desc' => $description,
                        'img' => $image,
                        'id' => $id
                    ]);
                    $message = "Đã cập nhật sản phẩm!";
                }
            } catch (PDOException $e) {
                $error = "Lỗi lưu dữ liệu: " . $e->getMessage();
            }
        }
    }
}

// Fetch Categories for Dropdown
$categories = $conn->query("SELECT * FROM categories WHERE is_active = 1")->fetchAll();

// Fetch Products with Category Name
$count_stmt = $conn->query("SELECT COUNT(*) FROM products WHERE is_active = 1");
$total_rows = $count_stmt->fetchColumn();
$limit = 10;
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
$offset = ($page - 1) * $limit;
$total_pages = ceil($total_rows / $limit);

$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.is_active = 1 
    ORDER BY p.id DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
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

<style>
    .lib-item {
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 4px;
        overflow: hidden;
        aspect-ratio: 1/1;
    }

    .lib-item:hover {
        border-color: var(--accent-color);
        opacity: 0.8;
    }

    .lib-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<script>
    let targetInputId = null;

    function openLibrarySelector(inputId) {
        targetInputId = inputId;
        document.getElementById('libModal').style.display = 'flex';
        loadLibraryImages();
    }

    function loadLibraryImages() {
        fetch('api/media_action.php?action=list')
            .then(res => res.json())
            .then(data => {
                const grid = document.getElementById('libGrid');
                grid.innerHTML = '';
                if (data.success && data.data.length > 0) {
                    data.data.forEach(file => {
                        const item = document.createElement('div');
                        item.className = 'lib-item';
                        item.onclick = () => selectImage(file.url);
                        item.innerHTML = `<img src="../${file.url}" title="${file.name}">`;
                        grid.appendChild(item);
                    });
                } else {
                    grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">Thư viện trống.</p>';
                }
            });
    }

    function selectImage(url) {
        if (targetInputId) {
            document.getElementById(targetInputId).value = url;
        }
        document.getElementById('libModal').style.display = 'none';
    }

    function openModal(mode, data = null) {
        document.getElementById('productModal').style.display = 'flex';
        if (mode === 'add') {
            document.getElementById('modalTitle').innerText = 'Thêm Sản Phẩm';
            document.getElementById('formAction').value = 'add';
            document.getElementById('productId').value = '';
            document.getElementById('pName').value = '';
            document.getElementById('pPrice').value = '';
            document.getElementById('pImage').value = '';
            document.getElementById('pDesc').value = '';
        } else {
            document.getElementById('modalTitle').innerText = 'Cập Nhật Sản Phẩm';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('productId').value = data.id;
            document.getElementById('pName').value = data.name;
            document.getElementById('pCat').value = data.category_id;
            document.getElementById('pPrice').value = data.base_price;
            document.getElementById('pImage').value = data.image;
            // Strip HTML tags for textarea if description has html
            document.getElementById('pDesc').value = data.description.replace(/<[^>]*>?/gm, '');
        }
    }

    function closeModal() {
        document.getElementById('productModal').style.display = 'none';
    }

    window.onclick = function (event) {
        if (event.target == document.getElementById('productModal')) {
            closeModal();
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>
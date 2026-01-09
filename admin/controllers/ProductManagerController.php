<?php
// admin/controllers/ProductManagerController.php

require_once '../config/db.php';
require_once 'includes/auth_check.php';

$page_title = "Quản Lý Sản Phẩm";

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
        $upload_dir = __DIR__ . '/../../uploads/'; // Absolute path
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
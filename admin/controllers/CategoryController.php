<?php
// admin/controllers/CategoryController.php

require_once '../config/db.php';
require_once 'includes/auth_check.php';

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
            $upload_dir = __DIR__ . '/../../uploads/'; // Absolute path (up 2 levels from admin/controllers)
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
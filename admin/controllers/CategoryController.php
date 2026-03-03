<?php
// admin/controllers/CategoryController.php

require_once '../config/db.php';
require_once 'includes/auth_check.php';
require_once 'includes/functions.php';

$page_title = "Quản Lý Danh Mục";

$message = "";
$error = "";

// Pagination
$limit = 10;
$current_page = isset($_GET['page_no']) ? intval($_GET['page_no']) : 1;

// 1. Delete
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = intval($_POST['id']);
    try {
        $stmt = $conn->prepare("UPDATE categories SET is_active = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $message = "Đã xóa danh mục!";
    } catch (PDOException $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// 2. Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && ($_POST['action'] == 'add' || $_POST['action'] == 'edit')) {
    $name = trim($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $image = trim($_POST['image']);

    if (empty($name)) {
        $error = "Vui lòng nhập tên danh mục.";
    } else {
        try {
            if ($_POST['action'] == 'add') {
                $stmt = $conn->prepare("INSERT INTO categories (name, slug, image) VALUES (:name, :slug, :img)");
                $stmt->execute(['name' => $name, 'slug' => $slug, 'img' => $image]);
                $message = "Thêm danh mục thành công!";
            } else {
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("UPDATE categories SET name = :name, slug = :slug, image = :img WHERE id = :id");
                $stmt->execute(['name' => $name, 'slug' => $slug, 'img' => $image, 'id' => $id]);
                $message = "Cập nhật danh mục thành công!";
            }
        } catch (PDOException $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// Pagination Logic
$stmt_count = $conn->query("SELECT COUNT(*) FROM categories WHERE is_active = 1");
$total_records = $stmt_count->fetchColumn();
$pagin = get_pagination_params($total_records, $current_page, $limit);
$offset = $pagin['offset'];
$total_pages = $pagin['total_pages'];
$current_page = $pagin['current_page'];

// Fetch categories
$stmt = $conn->prepare("SELECT * FROM categories WHERE is_active = 1 ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll();
?>
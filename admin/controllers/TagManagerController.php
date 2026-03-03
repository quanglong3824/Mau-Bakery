<?php
// admin/controllers/TagManagerController.php

require_once '../config/db.php';
require_once 'includes/auth_check.php';
require_once 'includes/functions.php';

$limit = 10;
$current_page = isset($_GET['page_no']) ? intval($_GET['page_no']) : 1;

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE
    if (isset($_POST['add_tag'])) {
        $name = $_POST['name'];
        $url = $_POST['url'];
        $icon = $_POST['icon'];
        $sort = $_POST['sort_order'];
        $stmt = $conn->prepare("INSERT INTO featured_tags (name, url, icon, sort_order) VALUES (:name, :url, :icon, :sort)");
        $stmt->execute(['name' => $name, 'url' => $url, 'icon' => $icon, 'sort' => $sort]);
        header("Location: tags.php?success=added");
        exit;
    }
    // UPDATE
    if (isset($_POST['edit_tag'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $url = $_POST['url'];
        $icon = $_POST['icon'];
        $sort = $_POST['sort_order'];
        $stmt = $conn->prepare("UPDATE featured_tags SET name = :name, url = :url, icon = :icon, sort_order = :sort WHERE id = :id");
        $stmt->execute(['name' => $name, 'url' => $url, 'icon' => $icon, 'sort' => $sort, 'id' => $id]);
        header("Location: tags.php?success=updated");
        exit;
    }
}

// DELETE / TOGGLE
if (isset($_GET['action'])) {
    $id = $_GET['id'];
    if ($_GET['action'] === 'delete') {
        $conn->prepare("DELETE FROM featured_tags WHERE id = :id")->execute(['id' => $id]);
        header("Location: tags.php?success=deleted");
        exit;
    }
    if ($_GET['action'] === 'toggle') {
        $conn->query("UPDATE featured_tags SET is_active = 1 - is_active WHERE id = $id");
        header("Location: tags.php");
        exit;
    }
}

// Pagination
$stmt_count = $conn->query("SELECT COUNT(*) FROM featured_tags");
$total_records = $stmt_count->fetchColumn();
$pagin = get_pagination_params($total_records, $current_page, $limit);
$offset = $pagin['offset'];
$total_pages = $pagin['total_pages'];
$current_page = $pagin['current_page'];

// Fetch Tags
$stmt = $conn->prepare("SELECT * FROM featured_tags ORDER BY sort_order ASC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tags = $stmt->fetchAll();

// Helpers for dropdowns
$categories = $conn->query("SELECT id, name, slug FROM categories WHERE is_active = 1")->fetchAll();
$products = $conn->query("SELECT id, name FROM products WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
?>
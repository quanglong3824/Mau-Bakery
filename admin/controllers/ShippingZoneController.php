<?php
// admin/controllers/ShippingZoneController.php

require_once '../config/db.php';
require_once 'includes/auth_check.php';
require_once 'includes/functions.php';

$limit = 10;
$current_page = isset($_GET['page_no']) ? intval($_GET['page_no']) : 1;

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE
    if (isset($_POST['add_zone'])) {
        $name = $_POST['name'];
        $fee = $_POST['fee'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO shipping_zones (name, fee, is_active) VALUES (:name, :fee, :is_active)");
        $stmt->execute(['name' => $name, 'fee' => $fee, 'is_active' => $is_active]);
        header("Location: shipping_zones.php?success=added");
        exit;
    }
    // UPDATE
    if (isset($_POST['edit_zone'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $fee = $_POST['fee'];
        $stmt = $conn->prepare("UPDATE shipping_zones SET name = :name, fee = :fee WHERE id = :id");
        $stmt->execute(['name' => $name, 'fee' => $fee, 'id' => $id]);
        header("Location: shipping_zones.php?success=updated");
        exit;
    }
}

// DELETE / TOGGLE
if (isset($_GET['action'])) {
    $id = $_GET['id'];
    if ($_GET['action'] === 'delete') {
        $conn->prepare("DELETE FROM shipping_zones WHERE id = :id")->execute(['id' => $id]);
        header("Location: shipping_zones.php?success=deleted");
        exit;
    }
    if ($_GET['action'] === 'toggle') {
        $conn->query("UPDATE shipping_zones SET is_active = 1 - is_active WHERE id = $id");
        header("Location: shipping_zones.php");
        exit;
    }
}

// Pagination
$stmt_count = $conn->query("SELECT COUNT(*) FROM shipping_zones");
$total_records = $stmt_count->fetchColumn();
$pagin = get_pagination_params($total_records, $current_page, $limit);
$offset = $pagin['offset'];
$total_pages = $pagin['total_pages'];
$current_page = $pagin['current_page'];

// Fetch Zones
$stmt = $conn->prepare("SELECT * FROM shipping_zones ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$zones = $stmt->fetchAll();
?>
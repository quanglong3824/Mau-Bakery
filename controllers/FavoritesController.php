<?php
// controllers/FavoritesController.php

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container mt-2 text-center'><div class='glass-panel' style='padding:50px;'><h3>Vui lòng đăng nhập để xem danh sách yêu thích</h3><a href='auth/login.php' class='btn-glass mt-1'>Đăng nhập</a></div></div>";
    return;
}

$user_id = $_SESSION['user_id'];
$favorites = [];

if (isset($conn)) {
    try {
        // Fetch Favorites Joined with Products
        $sql = "SELECT p.*, f.created_at as liked_at 
                FROM favorites f 
                JOIN products p ON f.product_id = p.id 
                WHERE f.user_id = :uid 
                ORDER BY f.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['uid' => $user_id]);
        $favorites = $stmt->fetchAll();
    } catch (PDOException $e) {
        $favorites = [];
    }
}
?>
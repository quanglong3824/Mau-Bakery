<?php
// controllers/HomeController.php

// Fetch Featured Products from Database
$products = [];
if (isset($conn)) {
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 LIMIT 4");
        $stmt->execute();
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "<!-- Error fetching products: " . $e->getMessage() . " -->";
    }
}
?>
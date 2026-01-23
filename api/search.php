<?php
// api/search.php
header('Content-Type: application/json');
require_once '../config/db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    echo json_encode([]);
    exit;
}

try {
    // Search by name, limit to 5-10 results for autocomplete
    $stmt = $conn->prepare("
        SELECT id, name, image, base_price, slug 
        FROM products 
        WHERE is_active = 1 AND name LIKE :query 
        ORDER BY name ASC 
        LIMIT 5
    ");
    $stmt->execute(['query' => "%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format results if needed (e.g. valid image path)
    foreach ($results as &$row) {
        $row['price_formatted'] = number_format($row['base_price'], 0, ',', '.') . 'đ';
        // Ensure image has correct path if relative
        // Assuming image stored reference is relative to project root or absolute URL.
        // If it's pure filename, prepend 'uploads/'. If full path/url, keep as is.
        // Based on previous views, it seems straightforward.
    }

    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

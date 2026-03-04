<?php
/**
 * API for Admin AI Statistics Dashboard
 */
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

try {
    // Check if user is admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }
    
    $period = isset($_GET['period']) ? $_GET['period'] : 'daily'; // daily, weekly, monthly
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : null;
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;
    
    // Set default date range based on period
    if (!$date_from || !$date_to) {
        switch ($period) {
            case 'weekly':
                $date_from = date('Y-m-d', strtotime('-7 days'));
                $date_to = date('Y-m-d');
                break;
            case 'monthly':
                $date_from = date('Y-m-d', strtotime('-30 days'));
                $date_to = date('Y-m-d');
                break;
            case 'daily':
            default:
                $date_from = date('Y-m-d');
                $date_to = date('Y-m-d');
                break;
        }
    }
    
    // Query for statistics
    $stmt = $conn->prepare("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as orders_count,
            SUM(total_amount) as revenue,
            AVG(total_amount) as avg_order_value
        FROM orders 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC
    ");
    $stmt->execute([$date_from, $date_to]);
    $daily_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Query for top selling products
    $stmt = $conn->prepare("
        SELECT 
            p.name,
            SUM(oi.quantity) as total_sold,
            SUM(oi.quantity * oi.price) as revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        GROUP BY p.id
        ORDER BY total_sold DESC
        LIMIT 10
    ");
    $stmt->execute([$date_from, $date_to]);
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Query for customer stats
    $stmt = $conn->prepare("
        SELECT 
            COUNT(DISTINCT user_id) as unique_customers,
            COUNT(*) as total_orders
        FROM orders
        WHERE DATE(created_at) BETWEEN ? AND ?
    ");
    $stmt->execute([$date_from, $date_to]);
    $customer_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate totals
    $total_revenue = array_sum(array_column($daily_stats, 'revenue'));
    $total_orders = array_sum(array_column($daily_stats, 'orders_count'));
    
    $response = [
        'period' => $period,
        'date_range' => [
            'from' => $date_from,
            'to' => $date_to
        ],
        'summary' => [
            'total_revenue' => floatval($total_revenue),
            'total_orders' => intval($total_orders),
            'unique_customers' => intval($customer_stats['unique_customers']),
            'avg_order_value' => $customer_stats['total_orders'] > 0 ? $total_revenue / $customer_stats['total_orders'] : 0
        ],
        'daily_stats' => $daily_stats,
        'top_products' => $top_products
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    error_log("AI Stats Error: " . $e->getMessage());
    echo json_encode(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
}
?>
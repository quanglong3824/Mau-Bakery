<?php
// admin/controllers/StatisticsController.php

require_once 'includes/auth_check.php';
require_once '../config/db.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'month'; // default is month

$where_clause = "";
$label_text = "";

switch ($filter) {
    case 'day':
        $where_clause = "DATE(o.created_at) = CURDATE()";
        $label_text = "Hôm nay (" . date('d/m/Y') . ")";
        break;
    case 'week':
        $where_clause = "YEARWEEK(o.created_at, 1) = YEARWEEK(CURDATE(), 1)";
        $label_text = "Tuần này";
        break;
    case 'month':
        $where_clause = "MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
        $label_text = "Tháng " . date('m/Y');
        break;
    case 'quarter':
        $where_clause = "QUARTER(o.created_at) = QUARTER(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
        $label_text = "Quý " . ceil(date('n') / 3) . " / " . date('Y');
        break;
    case 'year':
        $where_clause = "YEAR(o.created_at) = YEAR(CURDATE())";
        $label_text = "Năm " . date('Y');
        break;
    default:
        $where_clause = "MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
        $label_text = "Tháng " . date('m/Y');
}

if (isset($conn)) {
    // 1. Sales Chart (Always show last 7-10 entries/days for trend, but highlight the period)
    // We'll keep the 7-day trend as a fixed "Trend" chart, but update others based on filter
    $sales_chart = ['labels' => [], 'values' => []];
    for ($i = 9; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $display_date = date('d/m', strtotime("-$i days"));
        $stmt = $conn->prepare("SELECT SUM(total_amount) FROM orders WHERE status = 'completed' AND DATE(created_at) = :date");
        $stmt->execute(['date' => $date]);
        $revenue = $stmt->fetchColumn() ?: 0;
        $sales_chart['labels'][] = $display_date;
        $sales_chart['values'][] = (int)$revenue;
    }

    // 2. Sales by Category based on FILTER
    $stmt = $conn->query("SELECT c.name, SUM(oi.price * oi.quantity) as total 
                          FROM order_items oi 
                          JOIN products p ON oi.product_id = p.id 
                          JOIN categories c ON p.category_id = c.id 
                          JOIN orders o ON oi.order_id = o.id
                          WHERE o.status = 'completed' AND $where_clause
                          GROUP BY c.id");
    $category_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $category_chart = ['labels' => [], 'values' => []];
    foreach ($category_data as $row) {
        $category_chart['labels'][] = $row['name'];
        $category_chart['values'][] = (int)$row['total'];
    }

    // 3. Top 5 Best Selling Products based on FILTER
    $stmt = $conn->query("SELECT product_name, SUM(quantity) as total_sold 
                          FROM order_items oi
                          JOIN orders o ON oi.order_id = o.id
                          WHERE o.status = 'completed' AND $where_clause
                          GROUP BY product_name 
                          ORDER BY total_sold DESC LIMIT 5");
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Summary Stats for the filtered period
    $stmt = $conn->query("SELECT COUNT(*) as total_orders, SUM(total_amount) as total_revenue 
                          FROM orders o 
                          WHERE o.status = 'completed' AND $where_clause");
    $period_summary = $stmt->fetch(PDO::FETCH_ASSOC);

    // 5. Monthly Revenue for Bar Chart (Current Year)
    $monthly_chart = ['labels' => [], 'values' => []];
    for ($m = 1; $m <= 12; $m++) {
        $display_month = "T12" ? "Tháng $m" : "T$m";
        $stmt = $conn->prepare("SELECT SUM(total_amount) FROM orders 
                                WHERE status = 'completed' AND MONTH(created_at) = :m AND YEAR(created_at) = YEAR(CURDATE())");
        $stmt->execute(['m' => $m]);
        $revenue = $stmt->fetchColumn() ?: 0;
        $monthly_chart['labels'][] = "Tháng $m";
        $monthly_chart['values'][] = (int)$revenue;
    }
}
?>
<?php
session_start();
// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê AI - Mâu Bakery Admin</title>
    <link rel="stylesheet" href="assets/css/ai_statistics.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="ai-stats-container">
        <div class="stats-header">
            <h2>📊 Thống Kê Bán Hàng AI</h2>
            <div class="period-selector">
                <button class="period-btn active" data-period="daily">Ngày</button>
                <button class="period-btn" data-period="weekly">Tuần</button>
                <button class="period-btn" data-period="monthly">Tháng</button>
            </div>
            <div class="date-range-picker">
                <label>Từ: <input type="date" id="date-from"></label>
                <label>Đến: <input type="date" id="date-to"></label>
                <button id="custom-date-btn" class="btn-glass">Áp dụng</button>
            </div>
        </div>
        
        <div class="stats-summary" id="stats-summary">
            <div class="loading">Đang tải dữ liệu...</div>
        </div>
        
        <div class="charts-container">
            <div class="chart-box">
                <h3 class="chart-title">Doanh Thu Theo Ngày</h3>
                <canvas id="revenue-chart"></canvas>
            </div>
            <div class="chart-box">
                <h3 class="chart-title">Số Đơn Hàng</h3>
                <canvas id="orders-chart"></canvas>
            </div>
        </div>
        
        <div class="top-products">
            <h3 class="chart-title">Sản Phẩm Bán Chạy</h3>
            <div id="top-products-list">
                <div class="loading">Đang tải dữ liệu...</div>
            </div>
        </div>
    </div>

    <script src="assets/js/ai_statistics.js"></script>
</body>
</html>
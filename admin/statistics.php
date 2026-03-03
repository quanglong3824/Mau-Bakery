<?php
session_start();
require_once 'controllers/StatisticsController.php';
include 'includes/header.php';
?>

<div class="header-bar" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Thống Kê Kinh Doanh</h1>
        <p style="color: #6b7280; font-size: 0.9rem;">Dữ liệu: <?php echo $label_text; ?></p>
    </div>
    
    <div class="filter-group" style="display: flex; gap: 10px; background: white; padding: 5px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <a href="?filter=day" class="filter-btn <?php echo $filter == 'day' ? 'active' : ''; ?>">Ngày</a>
        <a href="?filter=week" class="filter-btn <?php echo $filter == 'week' ? 'active' : ''; ?>">Tuần</a>
        <a href="?filter=month" class="filter-btn <?php echo $filter == 'month' ? 'active' : ''; ?>">Tháng</a>
        <a href="?filter=quarter" class="filter-btn <?php echo $filter == 'quarter' ? 'active' : ''; ?>">Quý</a>
        <a href="?filter=year" class="filter-btn <?php echo $filter == 'year' ? 'active' : ''; ?>">Năm</a>
    </div>
</div>

<style>
.filter-btn {
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    color: #4b5563;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s;
}
.filter-btn:hover { background: #f3f4f6; color: var(--accent-color); }
.filter-btn.active { background: var(--accent-color); color: white; }

.stat-summary-card {
    background: white;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    text-align: center;
    flex: 1;
}
</style>

<!-- Summary Cards for Period -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-summary-card">
            <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 5px;">Tổng Đơn Hàng (<?php echo $filter; ?>)</p>
            <h2 style="font-size: 1.8rem; font-weight: 800; color: #111827;"><?php echo $period_summary['total_orders']; ?></h2>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-summary-card">
            <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 5px;">Tổng Doanh Thu (<?php echo $filter; ?>)</p>
            <h2 style="font-size: 1.8rem; font-weight: 800; color: #10b981;"><?php echo number_format($period_summary['total_revenue'], 0, ',', '.'); ?>đ</h2>
        </div>
    </div>
</div>

<div class="row">
    <!-- Trend Chart -->
    <div class="col-lg-8">
        <div class="glass-panel mb-4">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 24px;">Xu Hướng Doanh Thu 10 Ngày Gần Đây</h3>
            <div style="height: 300px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="glass-panel mb-4">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 24px;">Cơ Cấu Theo Danh Mục</h3>
            <div style="height: 300px;">
                <?php if (empty($category_chart['labels'])): ?>
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af;">Không có dữ liệu</div>
                <?php else: ?>
                    <canvas id="categoryChart"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="col-lg-12">
        <div class="glass-panel mb-4">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 24px;">Doanh Thu Từng Tháng Năm <?php echo date('Y'); ?></h3>
            <div style="height: 350px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-lg-12">
        <div class="glass-panel">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 24px;">Top Sản Phẩm Bán Chạy (<?php echo $label_text; ?>)</h3>
            <?php if (empty($top_products)): ?>
                <p style="text-align: center; color: #9ca3af; padding: 20px;">Không có dữ liệu bán hàng cho giai đoạn này.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên Sản Phẩm</th>
                            <th style="text-align: center;">Số Lượng Bán</th>
                            <th style="text-align: center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_products as $prod): ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($prod['product_name']); ?></td>
                            <td style="text-align: center; font-weight: 700; color: var(--accent-color);">
                                <?php echo $prod['total_sold']; ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-success">Hot</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.row { display: flex; flex-wrap: wrap; margin: 0 -10px; }
.col-md-6 { flex: 0 0 50%; padding: 0 10px; }
.col-lg-8 { flex: 0 0 66.666%; padding: 0 10px; }
.col-lg-4 { flex: 0 0 33.333%; padding: 0 10px; }
.col-lg-12 { flex: 0 0 100%; padding: 0 10px; }
.mb-4 { margin-bottom: 20px; }
@media (max-width: 992px) {
    .col-lg-8, .col-lg-4, .col-md-6 { flex: 0 0 100%; }
}
</style>

<script>
    // 1. Sales Trend Chart
    new Chart(document.getElementById('salesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($sales_chart['labels']); ?>,
            datasets: [{
                label: 'Doanh thu (đ)',
                data: <?php echo json_encode($sales_chart['values']); ?>,
                borderColor: '#e91e63',
                backgroundColor: 'rgba(233, 30, 99, 0.1)',
                borderWidth: 3,
                fill: true, tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Category Chart
    <?php if (!empty($category_chart['labels'])): ?>
    new Chart(document.getElementById('categoryChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($category_chart['labels']); ?>,
            datasets: [{
                data: <?php echo json_encode($category_chart['values']); ?>,
                backgroundColor: ['#f43f5e', '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#06b6d4']
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    <?php endif; ?>

    // 3. Monthly Chart
    new Chart(document.getElementById('monthlyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($monthly_chart['labels']); ?>,
            datasets: [{
                label: 'Doanh thu tháng (đ)',
                data: <?php echo json_encode($monthly_chart['values']); ?>,
                backgroundColor: '#6366f1',
                borderRadius: 8
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
let revenueChart, ordersChart;
let currentPeriod = 'daily';

// Initialize the dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadStats(currentPeriod);
    
    // Setup period buttons
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriod = this.dataset.period;
            loadStats(currentPeriod);
        });
    });
    
    // Setup custom date range
    document.getElementById('custom-date-btn').addEventListener('click', function() {
        const dateFrom = document.getElementById('date-from').value;
        const dateTo = document.getElementById('date-to').value;
        
        if (dateFrom && dateTo) {
            loadCustomDateRange(dateFrom, dateTo);
        } else {
            alert('Vui lòng chọn cả ngày bắt đầu và kết thúc');
        }
    });
});

// Load stats based on selected period
function loadStats(period) {
    // Set default dates based on period
    let dateFrom, dateTo;
    const today = new Date();
    
    switch(period) {
        case 'daily':
            dateFrom = today.toISOString().split('T')[0];
            dateTo = today.toISOString().split('T')[0];
            break;
        case 'weekly':
            const weekAgo = new Date(today);
            weekAgo.setDate(today.getDate() - 7);
            dateFrom = weekAgo.toISOString().split('T')[0];
            dateTo = today.toISOString().split('T')[0];
            break;
        case 'monthly':
            const monthAgo = new Date(today);
            monthAgo.setDate(today.getDate() - 30);
            dateFrom = monthAgo.toISOString().split('T')[0];
            dateTo = today.toISOString().split('T')[0];
            break;
    }
    
    // Update date inputs
    document.getElementById('date-from').value = dateFrom;
    document.getElementById('date-to').value = dateTo;
    
    fetchStats(dateFrom, dateTo);
}

// Load stats for custom date range
function loadCustomDateRange(dateFrom, dateTo) {
    fetchStats(dateFrom, dateTo);
}

// Fetch stats from API
function fetchStats(dateFrom, dateTo) {
    // Show loading indicators
    document.getElementById('stats-summary').innerHTML = '<div class="loading">Đang tải dữ liệu...</div>';
    document.getElementById('top-products-list').innerHTML = '<div class="loading">Đang tải dữ liệu...</div>';
    
    fetch(`../api/ai_stats.php?date_from=${dateFrom}&date_to=${dateTo}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }
            
            renderSummary(data.summary);
            renderCharts(data.daily_stats);
            renderTopProducts(data.top_products);
        })
        .catch(error => {
            console.error('Error fetching stats:', error);
        });
}

// Render summary cards
function renderSummary(summary) {
    const formatter = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    });
    
    document.getElementById('stats-summary').innerHTML = `
        <div class="stat-card">
            <div class="stat-label">Tổng Doanh Thu</div>
            <div class="stat-value">${formatter.format(summary.total_revenue)}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tổng Đơn Hàng</div>
            <div class="stat-value">${summary.total_orders}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Khách Hàng</div>
            <div class="stat-value">${summary.unique_customers}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Giá Trị Đơn TB</div>
            <div class="stat-value">${formatter.format(summary.avg_order_value)}</div>
        </div>
    `;
}

// Render charts
function renderCharts(dailyStats) {
    const dates = dailyStats.map(stat => stat.date);
    const revenues = dailyStats.map(stat => parseFloat(stat.revenue) || 0);
    const orders = dailyStats.map(stat => parseInt(stat.orders_count) || 0);
    
    // Destroy existing charts if they exist
    if (revenueChart) {
        revenueChart.destroy();
    }
    if (ordersChart) {
        ordersChart.destroy();
    }
    
    // Revenue chart
    const revenueCtx = document.getElementById('revenue-chart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Doanh Thu (VNĐ)',
                data: revenues,
                borderColor: '#ff7e5f',
                backgroundColor: 'rgba(255, 126, 95, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + '₫';
                        }
                    }
                }
            }
        }
    });
    
    // Orders chart
    const ordersCtx = document.getElementById('orders-chart').getContext('2d');
    ordersChart = new Chart(ordersCtx, {
        type: 'bar',
        data: {
            labels: dates,
            datasets: [{
                label: 'Số Đơn Hàng',
                data: orders,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Render top products table
function renderTopProducts(topProducts) {
    if (topProducts.length === 0) {
        document.getElementById('top-products-list').innerHTML = '<p>Không có dữ liệu sản phẩm bán chạy</p>';
        return;
    }
    
    const formatter = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    });
    
    let html = '<table><thead><tr><th>STT</th><th>Tên Sản Phẩm</th><th>Số Lượng Bán</th><th>Doanh Thu</th></tr></thead><tbody>';
    
    topProducts.forEach((product, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${product.name}</td>
                <td>${product.total_sold}</td>
                <td>${formatter.format(parseFloat(product.revenue) || 0)}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    document.getElementById('top-products-list').innerHTML = html;
}
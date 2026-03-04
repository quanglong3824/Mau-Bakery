// Load overview stats
document.addEventListener('DOMContentLoaded', function() {
    loadOverviewStats();
});

function loadOverviewStats() {
    fetch('../api/ai_stats.php?period=weekly')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('stats-overview').innerHTML = `<p>Lỗi: ${data.error}</p>`;
                return;
            }
            
            const formatter = new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            });
            
            document.getElementById('stats-overview').innerHTML = `
                <div class="stat-card">
                    <div class="stat-title">Tổng Doanh Thu (Tuần)</div>
                    <div class="stat-value">${formatter.format(data.summary.total_revenue)}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Tổng Đơn Hàng (Tuần)</div>
                    <div class="stat-value">${data.summary.total_orders}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Khách Hàng Mới (Tuần)</div>
                    <div class="stat-value">${data.summary.unique_customers}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Giá Trị Đơn TB</div>
                    <div class="stat-value">${formatter.format(data.summary.avg_order_value)}</div>
                </div>
                
                <h4 style="margin: 20px 0 10px;">Sản Phẩm Bán Chạy Nhất</h4>
                <div style="max-height: 200px; overflow-y: auto;">
                    ${(data.top_products && data.top_products.length > 0) ? 
                        data.top_products.slice(0, 5).map((product, idx) => 
                            `<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">
                                <span>${idx+1}. ${product.name}</span>
                                <span>${product.total_sold} cái</span>
                            </div>`
                        ).join('') : 
                        '<p>Không có dữ liệu</p>'
                    }
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            document.getElementById('stats-overview').innerHTML = '<p>Lỗi khi tải dữ liệu thống kê</p>';
        });
}

function sendMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add user message to chat
    addMessage(message, 'user');
    input.value = '';
    
    // Show typing indicator
    document.getElementById('typing-indicator').style.display = 'block';
    scrollToBottom();
    
    // Call AI API
    fetch('../api/ai_admin_advisor.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        // Hide typing indicator
        document.getElementById('typing-indicator').style.display = 'none';
        
        if (data.response) {
            addMessage(data.response, 'ai');
        } else if (data.error) {
            addMessage('Lỗi: ' + data.error, 'ai');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('typing-indicator').style.display = 'none';
        addMessage('Đã xảy ra lỗi khi kết nối với AI.', 'ai');
    });
}

function askAI(question) {
    document.getElementById('chat-input').value = question;
    sendMessage();
}

function handleKeyPress(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
}

function addMessage(text, sender) {
    const chatMessages = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('chat-message');
    messageDiv.classList.add(sender === 'user' ? 'user-message' : 'ai-message');
    messageDiv.textContent = text;
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
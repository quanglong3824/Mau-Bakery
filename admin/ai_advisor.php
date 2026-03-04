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
    <title>AI Cố Vấn Quản Lý - Mâu Bakery Admin</title>
    <link rel="stylesheet" href="assets/css/ai_advisor.css">
</head>
<body>
    <div class="ai-advisor-container">
        <div class="ai-advisor-header">
            <h2>🤖 AI Cố Vấn Quản Lý</h2>
            <p>Hệ thống phân tích dữ liệu bán hàng và đưa ra đề xuất kinh doanh thông minh</p>
        </div>
        
        <div class="ai-chat-container">
            <div class="stats-panel">
                <h3>📊 Thống Kê Tổng Quan</h3>
                <div id="stats-overview">
                    <div class="loading">Đang tải dữ liệu...</div>
                </div>
                
                <h3 style="margin-top: 30px;">⚡ Hành Động Nhanh</h3>
                <div class="quick-actions">
                    <div class="action-card" onclick="askAI('Phân tích tình hình kinh doanh hiện tại')">
                        <div class="action-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="action-text">Phân tích kinh doanh</div>
                    </div>
                    <div class="action-card" onclick="askAI('Đề xuất chiến lược marketing')">
                        <div class="action-icon"><i class="fas fa-bullhorn"></i></div>
                        <div class="action-text">Chiến lược marketing</div>
                    </div>
                    <div class="action-card" onclick="askAI('Sản phẩm nào bán chạy nhất tuần này?')">
                        <div class="action-icon"><i class="fas fa-star"></i></div>
                        <div class="action-text">Sản phẩm bán chạy</div>
                    </div>
                    <div class="action-card" onclick="askAI('Đề xuất combo sản phẩm')">
                        <div class="action-icon"><i class="fas fa-gift"></i></div>
                        <div class="action-text">Đề xuất combo</div>
                    </div>
                </div>
            </div>
            
            <div class="chat-panel">
                <div class="chat-messages" id="chat-messages">
                    <div class="chat-message ai-message">
                        Xin chào! Tôi là AI Cố Vấn Quản Lý của Mâu Bakery. Tôi có thể giúp bạn phân tích dữ liệu bán hàng, đưa ra đề xuất kinh doanh và trả lời các câu hỏi về hoạt động kinh doanh. Bạn muốn tôi giúp gì hôm nay?
                    </div>
                </div>
                <div class="typing-indicator" id="typing-indicator">
                    AI đang suy nghĩ...
                </div>
                <div class="chat-input-area">
                    <input type="text" class="chat-input" id="chat-input" placeholder="Nhập câu hỏi của bạn..." onkeypress="handleKeyPress(event)">
                    <button class="send-button" onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/ai_advisor.js"></script>
</body>
</html>
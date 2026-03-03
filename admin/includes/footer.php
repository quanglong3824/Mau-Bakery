    </main>
    <!-- AI Chatbot HTML -->
    <div class="ai-chatbot-container">
        <div class="ai-chatbot-toggle" style="background: linear-gradient(135deg, #444, #666);">
            <i class="fas fa-brain"></i>
        </div>
        <div class="ai-chatbot-window">
            <div class="ai-chatbot-header" style="background: linear-gradient(135deg, #444, #666); color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin:0; font-size:16px;"><i class="fas fa-brain"></i> Admin AI Helper</h3>
                <span class="ai-chatbot-close" style="cursor:pointer;"><i class="fas fa-times"></i></span>
            </div>
            <div class="ai-chatbot-messages">
                <div class="ai-message bot">Chào Admin! Tôi có thể giúp bạn xem báo cáo hoặc hỗ trợ quản lý?</div>
            </div>
            <div class="typing-indicator" style="padding: 0 15px; font-style: italic; color: #888; font-size: 12px; display:none;">AI đang xử lý...</div>
            <div class="ai-chatbot-input">
                <input type="text" placeholder="Hỏi tôi về doanh thu, đơn hàng...">
                <button class="ai-chatbot-send" style="background: #444;"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="../assets/css/ai_chatbot.css">
    <script src="assets/js/ai_admin.js"></script>

    <!-- Main Wrapper End -->

    <script>
        // Admin Chat Badge Logic
        function updateAdminChatBadge() {
            fetch('../api/chat_action.php?action=count_unread')
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('chat-badge-admin');
                    if(data.success && data.count > 0) {
                        badge.innerText = data.count;
                        badge.style.display = 'inline-block';
                    } else if(badge) {
                        badge.style.display = 'none';
                    }
                });
        }
        
        if(document.getElementById('chat-badge-admin')) {
            updateAdminChatBadge();
            setInterval(updateAdminChatBadge, 15000); // Check every 15s
        }
    </script>
</body>
</html>
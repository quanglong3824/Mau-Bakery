<footer class="glass-panel" style="border-radius: 50px 50px 0 0; margin-bottom: 0;">
    <div class="container" style="padding: 40px 20px;">
        <div class="footer-content">
            <div class="footer-col">
                <h3>Mâu Bakery</h3>
                <p>Mang đến những hương vị ngọt ngào nhất cho những khoảnh khắc đặc biệt của bạn. Được làm thủ công với
                    100% nguyên liệu tự nhiên.</p>
            </div>

            <div class="footer-col">
                <h3>Liên Hệ</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Đường Bánh Kem, Quận 1, TP.HCM</p>
                <p><i class="fas fa-phone"></i> 090 123 4567</p>
                <p><i class="fas fa-envelope"></i> hello@maubakery.com</p>
            </div>

            <div class="footer-col">
                <h3>Theo Dõi</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="copyright">
            <p>&copy; 2026 Mâu Bakery. All Rights Reserved. Designed by Student.</p>
        </div>
    </div>
</footer>
<!-- Back to Top Button -->
<button id="backToTopBtn" title="Lên đầu trang">
    <i class="fas fa-arrow-up"></i>
</button>

<link rel="stylesheet" href="assets/css/footer.css">
<script src="assets/js/footer.js"></script>

<!-- Unified Chatbot HTML -->
<div class="ai-chatbot-container">
    <div class="ai-chatbot-toggle">
        <i class="fas fa-comment-alt"></i>
    </div>
    <div class="ai-chatbot-window">
        <div class="ai-chatbot-header" style="background: linear-gradient(135deg, #ff7e5f, #feb47b); color: white; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <h3 style="margin:0; font-size:16px;"><i class="fas fa-birthday-cake"></i> Mâu Bakery Chat</h3>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="ai-status-indicator" style="display: flex; align-items: center; gap: 8px; font-size: 11px; background: rgba(0,0,0,0.1); padding: 4px 10px; border-radius: 15px;">
                    <span id="chat-mode-label">AI Trợ Lý</span>
                    <label class="switch" style="position: relative; display: inline-block; width: 34px; height: 18px; margin: 0;">
                        <input type="checkbox" id="ai-mode-toggle" checked style="opacity: 0; width: 0; height: 0;">
                        <span class="slider round"></span>
                    </label>
                </div>
                <span id="clear-chat-history" title="Xoá lịch sử chat" style="cursor:pointer; font-size: 14px; opacity: 0.8; transition: 0.3s;"><i class="fas fa-trash-alt"></i></span>
                <span class="ai-chatbot-close" style="cursor:pointer; font-size: 18px;"><i class="fas fa-times"></i></span>
            </div>
        </div>
        <div class="ai-chatbot-messages" id="unified-chat-messages">
            <div class="ai-message bot">Chào bạn! Mâu Bakery có thể giúp gì cho bạn?</div>
        </div>
        <div class="typing-indicator" id="ai-typing" style="padding: 0 15px; font-style: italic; color: #888; font-size: 11px; display:none;">AI đang soạn câu trả lời...</div>
        <div class="ai-chatbot-input">
            <input type="text" id="unified-chat-input" placeholder="Nhập tin nhắn...">
            <button class="ai-chatbot-send" id="unified-chat-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<style>
/* Style for the AI Toggle Switch */
.slider:before {
  position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%;
}
input:checked + .slider { background-color: #ff7e5f; }
input:checked + .slider:before { transform: translateX(14px); }
</style>

<link rel="stylesheet" href="assets/css/ai_chatbot.css">
<script src="assets/js/ai_chatbot.js"></script>
</body>

</html>
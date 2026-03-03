/* Unified AI & Human Chatbot JS */
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.querySelector('.ai-chatbot-toggle');
    const windowEl = document.querySelector('.ai-chatbot-window');
    const closeBtn = document.querySelector('.ai-chatbot-close');
    const sendBtn = document.getElementById('unified-chat-send');
    const input = document.getElementById('unified-chat-input');
    const messagesContainer = document.getElementById('unified-chat-messages');
    const typingIndicator = document.getElementById('ai-typing');
    const aiModeToggle = document.getElementById('ai-mode-toggle');
    const modeLabel = document.getElementById('chat-mode-label');
    const clearBtn = document.getElementById('clear-chat-history');

    if (!toggle) return;

    // Clear history
    if (clearBtn) {
        clearBtn.addEventListener('click', async () => {
            if (!roomId) return;
            if (confirm('Bạn có chắc chắn muốn xoá toàn bộ lịch sử trò chuyện này?')) {
                try {
                    const res = await fetch(`api/chat_action.php?action=delete&room_id=${roomId}`);
                    const data = await res.json();
                    if (data.success) {
                        messagesContainer.innerHTML = '<div class="ai-message bot">Đã xoá lịch sử. Mâu Bakery có thể giúp gì thêm cho bạn?</div>';
                        lastMessageCount = 0;
                    }
                } catch (e) {
                    console.error("Xoá thất bại", e);
                }
            }
        });
    }

    // Listen for toggle changes
    aiModeToggle.addEventListener('change', function() {
        if (this.checked) {
            modeLabel.innerText = "AI Trợ Lý";
            modeLabel.style.color = "#fff";
        } else {
            modeLabel.innerText = "Nhân Viên";
            modeLabel.style.color = "#ff7e5f";
        }
    });

    let roomId = localStorage.getItem('chat_room_id');
    let pollInterval = null;
    let lastMessageCount = 0;

    toggle.addEventListener('click', () => {
        windowEl.classList.toggle('active');
        if (windowEl.classList.contains('active')) {
            input.focus();
            startPolling();
        } else {
            stopPolling();
        }
    });

    closeBtn.addEventListener('click', () => {
        windowEl.classList.remove('active');
        stopPolling();
    });

    const addMessage = (text, sender) => {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('ai-message', sender === 'user' ? 'user' : 'bot');
        msgDiv.textContent = text;
        messagesContainer.appendChild(msgDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    const startPolling = () => {
        loadMessages();
        if (!pollInterval) {
            pollInterval = setInterval(loadMessages, 3000);
        }
    };

    const stopPolling = () => {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    };

    async function loadMessages() {
        try {
            const res = await fetch(`api/chat_action.php?action=fetch&room_id=${roomId || ''}&role=user`);
            const data = await res.json();
            if (data.success) {
                if (data.room_id) {
                    roomId = data.room_id;
                    localStorage.setItem('chat_room_id', roomId);
                }
                
                // Only re-render if message count changed
                if (data.messages.length !== lastMessageCount) {
                    messagesContainer.innerHTML = '';
                    data.messages.forEach(m => {
                        addMessage(m.message, m.role);
                    });
                    lastMessageCount = data.messages.length;
                }
            }
        } catch (e) {
            console.error("Failed to load messages", e);
        }
    }

    const sendMessage = async () => {
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        const isAiEnabled = aiModeToggle.checked;

        // 1. Save user message to DB (Human chat system)
        try {
            const res = await fetch('api/chat_action.php?action=send', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ room_id: roomId, message: text, role: 'user' })
            });
            const data = await res.json();
            if (data.success) {
                roomId = data.room_id;
                localStorage.setItem('chat_room_id', roomId);
            }
        } catch (e) {
            console.error("Failed to send message to DB", e);
        }

        // 2. If AI is enabled, get AI response
        if (isAiEnabled) {
            typingIndicator.style.display = 'block';
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            try {
                const aiRes = await fetch('api/ai_customer.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ message: text }),
                });
                const aiData = await aiRes.json();
                typingIndicator.style.display = 'none';

                if (aiData.response) {
                    // Add AI response directly to UI for immediate feedback
                    addMessage(aiData.response, 'bot');
                    
                    // Save AI response to DB so admin can see it
                    await fetch('api/chat_action.php?action=send', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ room_id: roomId, message: aiData.response, role: 'admin' })
                    });
                } else if (aiData.error) {
                    addMessage("Lỗi AI: " + aiData.error, 'bot');
                }
            } catch (error) {
                typingIndicator.style.display = 'none';
                addMessage("Lỗi kết nối AI: " + error.message, 'bot');
                console.error('AI Chat Error:', error);
            }
        } else {
            // If AI is disabled, just reload messages to show user's message
            loadMessages();
        }
    };

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});

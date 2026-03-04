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

        // Add user message to UI immediately to prevent disappearance
        addMessage(text, 'user');
        
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
                const aiRes = await fetch('api/enhanced_ai_customer.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ message: text }),
                });
                const aiData = await aiRes.json();
                typingIndicator.style.display = 'none';

                if (aiData.type) {
                    // Handle structured AI response
                    handleStructuredResponse(aiData);
                    
                    // Save AI response to DB so admin can see it
                    await fetch('api/chat_action.php?action=send', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ room_id: roomId, message: aiData.content || JSON.stringify(aiData), role: 'admin' })
                    });
                } else if (aiData.response) {
                    // Handle legacy response
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

    // Function to handle structured AI responses
    const handleStructuredResponse = (data) => {
        switch(data.type) {
            case 'message':
                addMessage(data.content, 'bot');
                break;
                
            case 'product_list':
                addProductList(data);
                break;
                
            case 'quick_replies':
                addQuickReplies(data);
                break;
                
            case 'order_form':
                addOrderForm(data);
                break;
                
            case 'order_success':
                addOrderSuccess(data);
                break;
                
            default:
                addMessage(data.content || JSON.stringify(data), 'bot');
                break;
        }
    };

    // Function to display product list with images
    const addProductList = (data) => {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('ai-message', 'bot');
        
        let content = `<div><strong>${data.content}</strong></div>`;
        
        if (data.products && data.products.length > 0) {
            content += '<div class="product-list-container" style="margin-top: 10px;">';
            
            data.products.forEach(product => {
                content += `
                <div class="product-item" style="display: flex; align-items: center; padding: 10px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 10px; background: white;">
                    <img src="${product.image}" alt="${product.name}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                    <div style="flex: 1;">
                        <div><strong>${product.name}</strong></div>
                        <div style="color: #ff7e5f; font-weight: bold;">${product.price.toLocaleString('vi-VN')}đ</div>
                        <button onclick="selectProduct(${product.id})" style="margin-top: 5px; background: #ff7e5f; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">Chọn bánh này</button>
                    </div>
                </div>`;
            });
            
            content += '</div>';
        }
        
        msgDiv.innerHTML = content;
        messagesContainer.appendChild(msgDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    // Function to display quick reply buttons
    const addQuickReplies = (data) => {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('ai-message', 'bot');
        
        msgDiv.innerHTML = `<div>${data.content}</div>`;
        messagesContainer.appendChild(msgDiv);
        
        if (data.quick_replies && data.quick_replies.length > 0) {
            const quickReplyDiv = document.createElement('div');
            quickReplyDiv.classList.add('quick-replies');
            quickReplyDiv.style.marginTop = '10px';
            quickReplyDiv.style.display = 'flex';
            quickReplyDiv.style.flexWrap = 'wrap';
            quickReplyDiv.style.gap = '5px';
            
            data.quick_replies.forEach(reply => {
                const btn = document.createElement('button');
                btn.textContent = reply;
                btn.style.background = '#f0f0f0';
                btn.style.border = '1px solid #ddd';
                btn.style.borderRadius = '15px';
                btn.style.padding = '6px 12px';
                btn.style.fontSize = '12px';
                btn.style.cursor = 'pointer';
                btn.onclick = () => {
                    input.value = reply;
                    sendMessage();
                };
                quickReplyDiv.appendChild(btn);
            });
            
            messagesContainer.appendChild(quickReplyDiv);
        }
        
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    // Function to display order form
    const addOrderForm = (data) => {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('ai-message', 'bot');
        
        msgDiv.innerHTML = `<div>${data.content}</div>
        <form id="order-form" style="margin-top: 15px;">
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">Họ tên:</label>
                <input type="text" id="customer-name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">Số điện thoại:</label>
                <input type="tel" id="customer-phone" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">Địa chỉ giao hàng:</label>
                <textarea id="delivery-address" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" rows="2" required></textarea>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Ghi chú (nếu có):</label>
                <textarea id="order-notes" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" rows="2"></textarea>
            </div>
            <button type="button" onclick="submitOrder()" style="background: #ff7e5f; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">Xác nhận đặt hàng</button>
        </form>`;
        
        messagesContainer.appendChild(msgDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    // Function to display order success message
    const addOrderSuccess = (data) => {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('ai-message', 'bot');
        
        msgDiv.innerHTML = `<div>${data.content}</div>
        <div style="margin-top: 15px; padding: 15px; background: #e8f5e9; border-radius: 8px; border: 1px solid #c8e6c9;">
            <div><strong>Mã đơn hàng: <span style="color: #2e7d32;">${data.order_info.order_id}</span></strong></div>
            <div>Tổng tiền: <strong>${data.order_info.amount.toLocaleString('vi-VN')}đ</strong></div>
            <div style="margin-top: 10px;">
                <button onclick="printInvoice('${data.order_info.order_id}')" style="background: #4caf50; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 10px;">In hóa đơn</button>
                <button onclick="downloadInvoice('${data.order_info.order_id}')" style="background: #2196f3; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Tải hóa đơn</button>
            </div>
        </div>`;
        
        messagesContainer.appendChild(msgDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    // Global functions for handling interactions
    window.selectProduct = function(productId) {
        input.value = `Tôi chọn sản phẩm ID: ${productId}`;
        sendMessage();
    };

    window.submitOrder = function() {
        const name = document.getElementById('customer-name').value;
        const phone = document.getElementById('customer-phone').value;
        const address = document.getElementById('delivery-address').value;
        const notes = document.getElementById('order-notes').value;
        
        if (!name || !phone || !address) {
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            return;
        }
        
        // Send order info to AI
        input.value = `Tôi xác nhận đặt hàng với thông tin:\n- Họ tên: ${name}\n- SĐT: ${phone}\n- Địa chỉ: ${address}\n- Ghi chú: ${notes}`;
        sendMessage();
    };

    window.printInvoice = function(orderId) {
        // Open invoice printing page in new window
        window.open(`index.php?page=print_invoice&order_id=${orderId}`, '_blank');
    };

    window.downloadInvoice = function(orderId) {
        // Create download link for invoice
        const link = document.createElement('a');
        link.href = `api/download_invoice.php?order_id=${orderId}`;
        link.download = `hoa_don_${orderId}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});

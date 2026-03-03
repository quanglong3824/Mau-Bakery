// assets/js/chat_user.js

(function() {
    // Create Chat UI Elements
    const chatHtml = `
        <div id="user-chat-wrapper" class="user-chat-wrapper">
            <div id="user-chat-bubble" class="user-chat-bubble">
                <i class="fas fa-comment-dots"></i>
                <span id="user-chat-notif" class="user-chat-notif" style="display:none;">!</span>
            </div>
            
            <div id="user-chat-box" class="user-chat-box" style="display:none;">
                <div class="chat-box-header">
                    <span>Hỗ trợ trực tuyến</span>
                    <button id="close-chat"><i class="fas fa-times"></i></button>
                </div>
                <div id="user-chat-messages" class="chat-box-messages">
                    <div class="msg admin">Xin chào! Mâu Bakery có thể giúp gì cho bạn?</div>
                </div>
                <div class="chat-box-input">
                    <input type="text" id="user-msg-input" placeholder="Nhập tin nhắn...">
                    <button id="send-user-msg"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', chatHtml);

    // CSS Styles
    const style = document.createElement('style');
    style.innerHTML = `
        .user-chat-wrapper { position: fixed; bottom: 30px; right: 30px; z-index: 9999; font-family: 'Quicksand', sans-serif; }
        .user-chat-bubble { width: 60px; height: 60px; background: var(--accent-color, #b19cd9); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s; }
        .user-chat-bubble:hover { transform: scale(1.1); }
        .user-chat-notif { position: absolute; top: 0; right: 0; background: #ef4444; width: 18px; height: 18px; border-radius: 50%; font-size: 12px; display: flex; align-items: center; justify-content: center; border: 2px solid white; }
        
        .user-chat-box { width: 320px; height: 450px; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: flex; flex-direction: column; overflow: hidden; position: absolute; bottom: 80px; right: 0; animation: slideUp 0.3s ease; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .chat-box-header { background: var(--accent-color, #b19cd9); color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; font-weight: 700; }
        .chat-box-header button { background: none; border: none; color: white; cursor: pointer; }
        
        .chat-box-messages { flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; background: #f9fafb; }
        .msg { max-width: 80%; padding: 8px 12px; border-radius: 12px; font-size: 0.9rem; line-height: 1.4; }
        .msg.user { align-self: flex-end; background: var(--accent-color, #b19cd9); color: white; border-bottom-right-radius: 2px; }
        .msg.admin { align-self: flex-start; background: #e5e7eb; color: #1f2937; border-bottom-left-radius: 2px; }
        
        .chat-box-input { padding: 10px; border-top: 1px solid #eee; display: flex; gap: 8px; }
        .chat-box-input input { flex: 1; border: 1px solid #ddd; border-radius: 20px; padding: 8px 15px; outline: none; font-size: 0.9rem; }
        .chat-box-input button { background: var(--accent-color, #b19cd9); color: white; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; }
    `;
    document.head.appendChild(style);

    // Elements
    const bubble = document.getElementById('user-chat-bubble');
    const box = document.getElementById('user-chat-box');
    const closeBtn = document.getElementById('close-chat');
    const sendBtn = document.getElementById('send-user-msg');
    const input = document.getElementById('user-msg-input');
    const msgContainer = document.getElementById('user-chat-messages');

    let roomId = localStorage.getItem('chat_room_id');
    let pollInterval = null;

    bubble.onclick = () => {
        box.style.display = 'flex';
        bubble.style.display = 'none';
        startPolling();
    };

    closeBtn.onclick = () => {
        box.style.display = 'none';
        bubble.style.display = 'flex';
        stopPolling();
    };

    function startPolling() {
        loadMessages();
        pollInterval = setInterval(loadMessages, 4000);
    }

    function stopPolling() {
        if(pollInterval) clearInterval(pollInterval);
    }

    function loadMessages() {
        fetch(`api/chat_action.php?action=fetch&room_id=${roomId || ''}&role=user`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    if(data.room_id) {
                        roomId = data.room_id;
                        localStorage.setItem('chat_room_id', roomId);
                    }
                    if(data.messages.length > 0) {
                        msgContainer.innerHTML = '';
                        data.messages.forEach(m => {
                            const div = document.createElement('div');
                            div.className = `msg ${m.role}`;
                            div.innerText = m.message;
                            msgContainer.appendChild(div);
                        });
                        msgContainer.scrollTop = msgContainer.scrollHeight;
                    }
                }
            });
    }

    function sendMessage() {
        const msg = input.value.trim();
        if(!msg) return;

        fetch('api/chat_action.php?action=send', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ room_id: roomId, message: msg, role: 'user' })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                input.value = '';
                roomId = data.room_id;
                localStorage.setItem('chat_room_id', roomId);
                loadMessages();
            }
        });
    }

    sendBtn.onclick = sendMessage;
    input.onkeypress = (e) => { if(e.key === 'Enter') sendMessage(); };

})();
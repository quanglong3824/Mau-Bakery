<?php
session_start();
if ($_SESSION['role'] !== 'admin') { header('Location: login.php'); exit; }
include 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/support.css">

<div class="header-bar">
    <h1 class="page-title">Hỗ Trợ Khách Hàng</h1>
</div>

<div class="chat-admin-container glass-panel">
    <div class="rooms-list" id="rooms-list">
        <div class="p-3 text-center">Đang tải danh sách...</div>
    </div>
    
    <div class="chat-main">
        <div id="chat-header" class="chat-header">
            Chọn một cuộc hội thoại để bắt đầu
        </div>
        <div id="chat-messages" class="chat-messages">
            <div class="no-chat-selected">
                <i class="fas fa-comments"></i>
                <p>Hãy chọn khách hàng cần hỗ trợ</p>
            </div>
        </div>
        <div class="chat-input-area" id="input-area" style="display: none;">
            <input type="text" id="admin-msg-input" placeholder="Nhập tin nhắn phản hồi...">
            <button onclick="sendAdminMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<style>
.chat-admin-container { display: flex; height: calc(100vh - 200px); padding: 0; overflow: hidden; }
.rooms-list { width: 300px; border-right: 1px solid #eee; overflow-y: auto; background: #f9fafb; }
.room-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; transition: all 0.2s; position: relative; }
.room-item:hover { background: #f3f4f6; }
.room-item.active { background: #fff; border-left: 4px solid var(--accent-color); }
.room-name { font-weight: 700; font-size: 0.95rem; margin-bottom: 5px; color: #111827; }
.room-last-msg { font-size: 0.85rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.room-unread { position: absolute; right: 15px; top: 15px; width: 10px; height: 10px; background: #ef4444; border-radius: 50%; }

.chat-main { flex: 1; display: flex; flex-direction: column; background: white; }
.chat-header { padding: 15px 25px; border-bottom: 1px solid #eee; font-weight: 700; color: var(--accent-color); }
.chat-messages { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #fff; }
.msg { max-width: 70%; padding: 10px 15px; border-radius: 15px; font-size: 0.95rem; line-height: 1.4; }
.msg.user { align-self: flex-start; background: #f3f4f6; color: #1f2937; border-bottom-left-radius: 2px; }
.msg.admin { align-self: flex-end; background: var(--accent-color); color: white; border-bottom-right-radius: 2px; }
.no-chat-selected { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #9ca3af; }
.no-chat-selected i { font-size: 4rem; margin-bottom: 15px; }

.chat-input-area { padding: 15px 20px; border-top: 1px solid #eee; display: flex; gap: 10px; }
.chat-input-area input { flex: 1; padding: 10px 15px; border: 1px solid #e5e7eb; border-radius: 25px; outline: none; }
.chat-input-area button { width: 40px; height: 40px; border-radius: 50%; background: var(--accent-color); color: white; border: none; cursor: pointer; }
</style>

<script>
let currentRoomId = null;
let pollInterval = null;

function loadRooms() {
    fetch('../api/chat_action.php?action=list_rooms')
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const list = document.getElementById('rooms-list');
                list.innerHTML = '';
                data.rooms.forEach(room => {
                    const div = document.createElement('div');
                    div.className = `room-item ${currentRoomId == room.id ? 'active' : ''}`;
                    div.onclick = () => selectRoom(room.id, room.customer_name);
                    div.innerHTML = `
                        <div class="room-name">${room.customer_name}</div>
                        <div class="room-last-msg">${room.last_message || 'Chưa có tin nhắn'}</div>
                        ${room.is_read_by_admin == 0 ? '<div class="room-unread"></div>' : ''}
                    `;
                    list.appendChild(div);
                });
            }
        });
}

function selectRoom(id, name) {
    currentRoomId = id;
    document.getElementById('chat-header').innerText = `Đang hỗ trợ: ${name}`;
    document.getElementById('input-area').style.display = 'flex';
    loadMessages();
    loadRooms(); // Refresh unread status
    
    if(pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(loadMessages, 3000);
}

function loadMessages() {
    if(!currentRoomId) return;
    fetch(`../api/chat_action.php?action=fetch&room_id=${currentRoomId}&role=admin`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const container = document.getElementById('chat-messages');
                container.innerHTML = '';
                data.messages.forEach(m => {
                    const div = document.createElement('div');
                    div.className = `msg ${m.role}`;
                    div.innerText = m.message;
                    container.appendChild(div);
                });
                container.scrollTop = container.scrollHeight;
            }
        });
}

function sendAdminMessage() {
    const input = document.getElementById('admin-msg-input');
    const msg = input.value.trim();
    if(!msg || !currentRoomId) return;

    fetch('../api/chat_action.php?action=send', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ room_id: currentRoomId, message: msg, role: 'admin' })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            input.value = '';
            loadMessages();
            loadRooms();
        }
    });
}

// Initial Load
loadRooms();
setInterval(loadRooms, 10000); // Poll rooms list every 10s
</script>

<?php include 'includes/footer.php'; ?>
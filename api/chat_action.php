<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? null;
$session_id = session_id();

try {
    // 1. GỬI TIN NHẮN
    if ($action === 'send') {
        $data = json_decode(file_get_contents('php://input'), true);
        $message = trim($data['message'] ?? '');
        $role = $data['role'] ?? 'user'; // 'user' or 'admin'
        $room_id = $data['room_id'] ?? null;

        if (empty($message)) throw new Exception("Tin nhắn trống");

        // Tìm hoặc tạo Room cho User
        if ($role === 'user' && !$room_id) {
            $stmt = $conn->prepare("SELECT id FROM chat_rooms WHERE (user_id = :uid AND user_id IS NOT NULL) OR session_id = :sid ORDER BY updated_at DESC LIMIT 1");
            $stmt->execute(['uid' => $user_id, 'sid' => $session_id]);
            $room_id = $stmt->fetchColumn();

            if (!$room_id) {
                $name = $_SESSION['full_name'] ?? 'Khách vãng lai';
                $stmt = $conn->prepare("INSERT INTO chat_rooms (user_id, session_id, customer_name, status) VALUES (?, ?, ?, 'open')");
                $stmt->execute([$user_id, $session_id, $name]);
                $room_id = $conn->lastInsertId();
            }
        }

        if (!$room_id) throw new Exception("Thiếu Room ID");

        // Chèn tin nhắn
        $stmt = $conn->prepare("INSERT INTO chat_messages (room_id, sender_role, message) VALUES (?, ?, ?)");
        $stmt->execute([$room_id, $role, $message]);

        // Cập nhật Room (last message & unread status)
        $is_admin = ($role === 'admin');
        $sql_up = "UPDATE chat_rooms SET last_message = ?, updated_at = NOW(), 
                   is_read_by_admin = ?, is_read_by_user = ? WHERE id = ?";
        $stmt_up = $conn->prepare($sql_up);
        $stmt_up->execute([$message, $is_admin ? 1 : 0, $is_admin ? 0 : 1, $room_id]);

        echo json_encode(['success' => true, 'room_id' => $room_id]);
    }

    // 2. LẤY TIN NHẮN (Polling)
    elseif ($action === 'fetch') {
        $room_id = $_GET['room_id'] ?? null;
        if (!$room_id && $user_id) {
            $stmt = $conn->prepare("SELECT id FROM chat_rooms WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1");
            $stmt->execute([$user_id]);
            $room_id = $stmt->fetchColumn();
        } elseif (!$room_id) {
            $stmt = $conn->prepare("SELECT id FROM chat_rooms WHERE session_id = ? ORDER BY updated_at DESC LIMIT 1");
            $stmt->execute([$session_id]);
            $room_id = $stmt->fetchColumn();
        }

        if (!$room_id) {
            echo json_encode(['success' => true, 'messages' => []]);
            exit;
        }

        // Lấy tin nhắn
        $stmt = $conn->prepare("SELECT sender_role as role, message, created_at FROM chat_messages WHERE room_id = ? ORDER BY created_at ASC");
        $stmt->execute([$room_id]);
        $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đánh dấu đã đọc nếu role fetch là role tương ứng
        $fetch_role = $_GET['role'] ?? 'user';
        if ($fetch_role === 'admin') {
            $conn->prepare("UPDATE chat_rooms SET is_read_by_admin = 1 WHERE id = ?")->execute([$room_id]);
        } else {
            $conn->prepare("UPDATE chat_rooms SET is_read_by_user = 1 WHERE id = ?")->execute([$room_id]);
        }

        echo json_encode(['success' => true, 'messages' => $msgs, 'room_id' => $room_id]);
    }

    // 3. LẤY DANH SÁCH PHÒNG (Cho Admin)
    elseif ($action === 'list_rooms') {
        if ($_SESSION['role'] !== 'admin') throw new Exception("Không có quyền");
        $stmt = $conn->query("SELECT * FROM chat_rooms ORDER BY updated_at DESC");
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'rooms' => $rooms]);
    }

    // 4. LẤY TỔNG SỐ TIN CHƯA ĐỌC (Cho Badge)
    elseif ($action === 'count_unread') {
        $stmt = $conn->query("SELECT COUNT(*) FROM chat_rooms WHERE is_read_by_admin = 0");
        $count = $stmt->fetchColumn();
        echo json_encode(['success' => true, 'count' => (int)$count]);
    }

    // 5. XOÁ LỊCH SỬ CHAT (Cho User)
    elseif ($action === 'delete') {
        $room_id = $_GET['room_id'] ?? null;
        if (!$room_id) throw new Exception("Thiếu Room ID");

        // Xoá tất cả tin nhắn trong room
        $stmt = $conn->prepare("DELETE FROM chat_messages WHERE room_id = ?");
        $stmt->execute([$room_id]);

        // Cập nhật lại thông tin Room
        $stmt = $conn->prepare("UPDATE chat_rooms SET last_message = '', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$room_id]);

        echo json_encode(['success' => true, 'message' => 'Đã xoá lịch sử chat']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
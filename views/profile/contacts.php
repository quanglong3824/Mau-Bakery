<?php
// views/profile/contacts.php
?>
<div id="contacts">
    <h2 class="content-title">Lịch Sử Liên Hệ</h2>
    <div class="table-container">
        <?php if (empty($contacts_history)): ?>
            <p style="text-align: center; padding: 20px; color: #777;">Bạn chưa gửi liên hệ nào.</p>
        <?php else: ?>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nội dung</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts_history as $contact): ?>
                        <tr>
                            <td style="font-weight: 600;">#
                                <?php echo $contact['id']; ?>
                            </td>
                            <td style="max-width: 300px;">
                                <div style="white-space: pre-wrap; word-break: break-word; font-size: 0.95rem;">
                                    <?php
                                    // Simple truncation or full view
                                    $msg_display = htmlspecialchars($contact['message']);
                                    echo strlen($msg_display) > 100 ? substr($msg_display, 0, 100) . '...' : $msg_display;
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php echo isset($contact['created_at']) ? date('d/m/Y H:i', strtotime($contact['created_at'])) : 'N/A'; ?>
                            </td>
                            <td>
                                <span class="status-badge status-pending">Đã gửi</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
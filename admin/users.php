<?php
session_start();
// Include Controller
require_once 'controllers/UserManagerController.php';

require_once 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/users.css">

<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users"></i> Quản Lý Người Dùng</h2>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="glass-panel p-0 overflow-hidden">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Thông tin cá nhân</th>
                    <th>Liên hệ</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>#
                            <?php echo $u['id']; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="user-name">
                                        <?php echo htmlspecialchars($u['full_name']); ?>
                                    </div>
                                    <div class="user-username">@
                                        <?php echo htmlspecialchars($u['username']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div><i class="fas fa-envelope text-muted"></i>
                                <?php echo htmlspecialchars($u['email']); ?>
                            </div>
                            <div><i class="fas fa-phone text-muted"></i>
                                <?php echo htmlspecialchars($u['phone']); ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($u['role'] == 'admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-info text-white">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Đã khóa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['role'] != 'admin'): // Don't allow banning main admins easily ?>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                    <?php if ($u['is_active']): ?>
                                        <input type="hidden" name="status" value="0">
                                        <button class="btn btn-sm btn-warning text-white" title="Khóa tài khoản">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    <?php else: ?>
                                        <input type="hidden" name="status" value="1">
                                        <button class="btn btn-sm btn-success" title="Mở khóa">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
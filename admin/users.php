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
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Thêm Người Dùng
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger">
            <?php echo $error_msg; ?>
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
                        <td>#<?php echo $u['id']; ?></td>
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
                            <?php if ($u['role'] != 'admin'): ?>
                                <div class="action-buttons">
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-info text-white"
                                        onclick='openEditModal(<?php echo json_encode($u); ?>)' title="Sửa thông tin">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Toggle Status Button -->
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

                                    <!-- Delete Button -->
                                    <form method="POST" style="display:inline-block;"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này? Hành động này không thể hoàn tác!');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <button class="btn btn-sm btn-danger" title="Xóa người dùng">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Quản trị viên</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal-overlay">
    <div class="modal-box glass-panel">
        <h3 class="modal-title">Thêm Người Dùng Mới</h3>
        <form method="POST">
            <input type="hidden" name="action" value="create_user">

            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Tên đăng nhập (Username)</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Vai trò</label>
                <select name="role" class="form-control">
                    <option value="user">Người dùng (User)</option>
                    <option value="admin">Quản trị viên (Admin)</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Thêm mới</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal-overlay">
    <div class="modal-box glass-panel">
        <h3 class="modal-title">Sửa Thông Tin Người Dùng</h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_user">
            <input type="hidden" name="id" id="edit_id">

            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Username (Không thể sửa)</label>
                <input type="text" name="username" id="edit_username" class="form-control" readonly
                    style="background: #f0f0f0;">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="phone" id="edit_phone" class="form-control">
            </div>

            <div class="form-group">
                <label>Mật khẩu Mới (Để trống nếu không đổi)</label>
                <input type="password" name="password" class="form-control" placeholder="******">
            </div>

            <div class="form-group">
                <label>Vai trò</label>
                <select name="role" id="edit_role" class="form-control">
                    <option value="user">Người dùng (User)</option>
                    <option value="admin">Quản trị viên (Admin)</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- Load separated JS -->
<script src="assets/js/users.js"></script>

<?php require_once 'includes/footer.php'; ?>
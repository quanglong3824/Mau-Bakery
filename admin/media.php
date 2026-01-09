<?php
session_start();
// Include Controller
require_once 'controllers/MediaManagerController.php';

require_once 'includes/header.php';
?>

<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-images"></i> Thư Viện Ảnh</h2>
        <div>
            <span id="uploadStatus" style="margin-right: 15px; font-weight: 600; color: var(--accent-color);"></span>
            <button class="btn btn-primary btn-glass" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-cloud-upload-alt"></i> Tải Ảnh Lên
            </button>
            <input type="file" id="fileInput" multiple style="display: none;" accept="image/*">
        </div>
    </div>

    <!-- Drop Zone -->
    <div id="dropZone" class="glass-panel"
        style="border: 2px dashed #ccc; text-align: center; padding: 40px; margin-bottom: 20px; transition: all 0.2s;">
        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #ccc; margin-bottom: 10px;"></i>
        <p style="color: #666; margin: 0;">Kéo & thả ảnh vào đây hoặc bấm nút tải lên.</p>
        <p style="font-size: 0.8rem; color: #999;">Hỗ trợ JPG, PNG, WEBP, GIF</p>
    </div>

    <!-- Media Grid -->
    <div id="mediaGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px;">
        <!-- Loaded via JS -->
    </div>
</div>

<!-- Preview/Rename Modal -->
<div id="mediaModal" class="modal-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel"
        style="background: white; width: 90%; max-width: 500px; padding: 25px; position: relative;">
        <button onclick="closeMediaModal()"
            style="position: absolute; right: 15px; top: 15px; border: none; background: none; font-size: 1.2rem; cursor: pointer;"><i
                class="fas fa-times"></i></button>

        <h3 class="mb-3">Chi tiết ảnh</h3>
        <div style="text-align: center; background: #f3f4f6; margin-bottom: 20px; border-radius: 8px; padding: 10px;">
            <img id="previewImg" src="" style="max-width: 100%; max-height: 250px; object-fit: contain;">
        </div>

        <div class="mb-3">
            <label class="form-label">Tên file</label>
            <div class="d-flex gap-2">
                <input type="text" id="fileNameInput" class="form-control">
                <button class="btn btn-info text-white" onclick="renameFile()" title="Đổi tên"><i
                        class="fas fa-save"></i></button>
            </div>
            <small class="text-muted">Tự động tạo slug khi đổi tên.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">URL</label>
            <div class="d-flex gap-2">
                <input type="text" id="fileUrlInput" class="form-control" readonly>
                <button class="btn btn-secondary" onclick="copyUrl()" title="Copy URL"><i
                        class="fas fa-copy"></i></button>
            </div>
        </div>

        <div class="text-end">
            <button class="btn btn-danger" onclick="deleteFile()"><i class="fas fa-trash"></i> Xóa Ảnh</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="assets/css/media.css">

<script src="assets/js/media.js"></script>

<?php require_once 'includes/footer.php'; ?>
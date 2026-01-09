<?php
session_start();
require_once '../config/db.php';
require_once 'includes/auth_check.php';

$page_title = "Thư Viện Ảnh";
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

<style>
    .media-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        aspect-ratio: 1/1;
        cursor: pointer;
        background: #eee;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .media-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-color);
    }

    .media-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .media-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 5px;
        font-size: 0.7rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: center;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .media-item:hover .media-info {
        opacity: 1;
    }

    #dropZone.dragover {
        border-color: var(--accent-color) !important;
        background: rgba(177, 156, 217, 0.1) !important;
    }
</style>

<script>
    let currentFile = null;

    // Init
    document.addEventListener('DOMContentLoaded', loadMedia);

    // Load Files
    function loadMedia() {
        fetch('api/media_action.php?action=list')
            .then(res => res.json())
            .then(data => {
                const grid = document.getElementById('mediaGrid');
                grid.innerHTML = '';
                if (data.success && data.data.length > 0) {
                    data.data.forEach(file => {
                        const item = document.createElement('div');
                        item.className = 'media-item glass-panel p-0';
                        item.onclick = () => openMediaModal(file);
                        item.innerHTML = `
                            <img src="../${file.url}" loading="lazy">
                            <div class="media-info">${file.name}</div>
                        `;
                        grid.appendChild(item);
                    });
                } else {
                    grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999;">Thư viện trống.</p>';
                }
            });
    }

    // Modal Actions
    function openMediaModal(file) {
        currentFile = file;
        document.getElementById('mediaModal').style.display = 'flex';
        document.getElementById('previewImg').src = '../' + file.url;
        document.getElementById('fileNameInput').value = file.name; // Simple name editing, strict validation in backend
        document.getElementById('fileUrlInput').value = file.url; // Show relative path for easy DB usage
    }

    function closeMediaModal() {
        document.getElementById('mediaModal').style.display = 'none';
        currentFile = null;
    }

    // Rename
    function renameFile() {
        if (!currentFile) return;
        const newName = document.getElementById('fileNameInput').value;
        if (newName === currentFile.name) return;

        const formData = new FormData();
        formData.append('action', 'rename');
        formData.append('old_name', currentFile.name);
        formData.append('new_name', newName);

        fetch('api/media_action.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Đổi tên thành công!');
                    loadMedia();
                    closeMediaModal();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            });
    }

    // Delete
    function deleteFile() {
        if (!currentFile || !confirm('Bạn chắc chắn muốn xóa ảnh này?')) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('filename', currentFile.name);

        fetch('api/media_action.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadMedia();
                    closeMediaModal();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            });
    }

    function copyUrl() {
        const urlField = document.getElementById('fileUrlInput');
        urlField.select();
        document.execCommand('copy');
        alert('Đã copy đường dẫn: ' + urlField.value);
    }

    // Upload Handling (File Input)
    document.getElementById('fileInput').addEventListener('change', function (e) {
        handleUpload(e.target.files);
    });

    // Drag & Drop
    const dropZone = document.getElementById('dropZone');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        handleUpload(e.dataTransfer.files);
    });

    function handleUpload(files) {
        if (files.length === 0) return;

        const formData = new FormData();
        formData.append('action', 'upload');

        // RAM Protection: Send strictly 30 files max per request if needed, or simple loop.
        // For standard usage, PHP post_max_size is usually the limit. 
        // Here we just send them all. Assuming User doesn't drag 1000 files.
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        const status = document.getElementById('uploadStatus');
        status.innerText = 'Đang tải lên...';

        fetch('api/media_action.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error("Server Error: " + text.substring(0, 100) + "...");
                }
            })
            .then(data => {
                status.innerText = '';
                if (data.success) {
                    loadMedia();
                    if (data.data.errors && data.data.errors.length > 0) {
                        alert('Một số file lỗi: \n' + data.data.errors.join('\n'));
                    }
                } else {
                    alert('Lỗi Upload: ' + data.message);
                }
            })
            .catch(err => {
                status.innerText = '';
                console.error(err);
                alert('Lỗi: ' + err.message);
            });
    }
</script>

<?php require_once 'includes/footer.php'; ?>
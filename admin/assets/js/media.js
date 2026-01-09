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

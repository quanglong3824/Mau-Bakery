let targetInputId = null;

function openLibrarySelector(inputId) {
    targetInputId = inputId;
    document.getElementById('libModal').style.display = 'flex';
    loadLibraryImages();
}

function loadLibraryImages() {
    fetch('api/media_action.php?action=list')
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('libGrid');
            grid.innerHTML = '';
            if (data.success && data.data.length > 0) {
                data.data.forEach(file => {
                    const item = document.createElement('div');
                    item.className = 'lib-item';
                    item.onclick = () => selectImage(file.url);
                    item.innerHTML = `<img src="../${file.url}" title="${file.name}">`;
                    grid.appendChild(item);
                });
            } else {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">Thư viện trống.</p>';
            }
        });
}

function selectImage(url) {
    if (targetInputId) {
        document.getElementById(targetInputId).value = url;
    }
    document.getElementById('libModal').style.display = 'none';
}

function openModal(mode, data = null) {
    document.getElementById('productModal').style.display = 'flex';
    if (mode === 'add') {
        document.getElementById('modalTitle').innerText = 'Thêm Sản Phẩm';
        document.getElementById('formAction').value = 'add';
        document.getElementById('productId').value = '';
        document.getElementById('pName').value = '';
        document.getElementById('pPrice').value = '';
        document.getElementById('pImage').value = '';
        document.getElementById('pDesc').value = '';
    } else {
        document.getElementById('modalTitle').innerText = 'Cập Nhật Sản Phẩm';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('productId').value = data.id;
        document.getElementById('pName').value = data.name;
        document.getElementById('pCat').value = data.category_id;
        document.getElementById('pPrice').value = data.base_price;
        document.getElementById('pImage').value = data.image;
        // Strip HTML tags for textarea if description has html
        document.getElementById('pDesc').value = data.description.replace(/<[^>]*>?/gm, '');
    }
}

function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

window.onclick = function (event) {
    if (event.target == document.getElementById('productModal')) {
        closeModal();
    }
}

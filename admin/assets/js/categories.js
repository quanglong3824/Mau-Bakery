function openModal(mode, data = null) {
    document.getElementById('catModal').style.display = 'flex';
    if (mode === 'add') {
        document.getElementById('modalTitle').innerText = 'Thêm Danh Mục';
        document.getElementById('formAction').value = 'add';
        document.getElementById('catId').value = '';
        document.getElementById('catName').value = '';
        document.getElementById('catSlug').value = '';
        document.getElementById('catImage').value = '';
    } else {
        document.getElementById('modalTitle').innerText = 'Cập Nhật Danh Mục';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('catId').value = data.id;
        document.getElementById('catName').value = data.name;
        document.getElementById('catSlug').value = data.slug;
        document.getElementById('catImage').value = data.image;
    }
}

function closeModal() {
    document.getElementById('catModal').style.display = 'none';
}

function generateSlug() {
    let name = document.getElementById('catName').value;
    let slug = name.toLowerCase()
        .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
        .replace(/[đĐ]/g, "d")
        .replace(/[^a-z0-9\s-]/g, '')
        .trim().replace(/\s+/g, '-');
    document.getElementById('catSlug').value = slug;
}

window.onclick = function (event) {
    if (event.target == document.getElementById('catModal')) {
        closeModal();
    }
}

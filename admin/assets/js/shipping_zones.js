// admin/assets/js/shipping_zones.js

function openAddModal() {
    document.getElementById('addZoneModal').style.display = 'flex';
}

function openEditModal(zone) {
    document.getElementById('edit_id').value = zone.id;
    document.getElementById('edit_name').value = zone.name;
    document.getElementById('edit_fee').value = zone.fee;
    
    document.getElementById('editZoneModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function confirmDelete(id) {
    if(confirm('Bạn có chắc chắn muốn xóa khu vực ship này? Hành động này không thể hoàn tác.')) {
        window.location.href = `shipping_zones.php?action=delete&id=${id}`;
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
}

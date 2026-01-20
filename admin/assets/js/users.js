// admin/assets/js/users.js

function openAddModal() {
    const modal = document.getElementById('addUserModal');
    if (modal) {
        modal.classList.add('active');
    }
}

function openEditModal(user) {
    const modal = document.getElementById('editUserModal');
    if (modal) {
        // Populate form fields
        document.getElementById('edit_id').value = user.id;
        document.getElementById('edit_full_name').value = user.full_name;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_phone').value = user.phone;
        document.getElementById('edit_role').value = user.role;
        
        // Show modal
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal when clicking outside the content box
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.classList.remove('active');
    }
}

// Optional: Add Escape key listener
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const activeModals = document.querySelectorAll('.modal-overlay.active');
        activeModals.forEach(modal => {
            modal.classList.remove('active');
        });
    }
});

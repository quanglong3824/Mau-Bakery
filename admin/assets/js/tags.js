/**
 * admin/assets/js/tags.js
 * Handle Tags Logic: Modal, Form Submission, URL Generation
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Modal Elements
    const addModal = document.getElementById('addTagModal');
    const editModal = document.getElementById('editTagModal');
    
    // Open Add Modal
    document.querySelector('.btn-add').addEventListener('click', function(e) {
        e.preventDefault();
        addModal.style.display = 'flex';
    });

    // Close Modals
    document.querySelectorAll('.close-modal, .modal-overlay').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) {
                addModal.style.display = 'none';
                editModal.style.display = 'none';
            }
        });
    });

    // --- URL Auto-Generator Logic ---
    function setupUrlGenerator(prefix) {
        const typeSelect = document.getElementById(prefix + 'linkType');
        const valueInput = document.getElementById(prefix + 'linkValue'); // This might be a select or input needed
        const urlInput = document.getElementById(prefix + 'url'); // The actual hidden or visible URL field
        const specificSelect = document.getElementById(prefix + 'specificSelect'); // Container for dynamic select

        if (!typeSelect) return;

        typeSelect.addEventListener('change', function() {
            const type = this.value;
            specificSelect.innerHTML = ''; // Clear previous input
            
            if (type === 'custom') {
                specificSelect.innerHTML = `<input type="text" class="form-control" placeholder="Nhập đường dẫn tùy ý (e.g. index.php?page=about)" onchange="document.getElementById('${prefix}url').value = this.value">`;
            } 
            else if (type === 'category') {
                 // Clone the category list from a hidden source or fetch via AJAX?
                 // For simplicity, we assume PHP rendered the options into a hidden SELECT we can clone, or we use a simple input for now.
                 // Let's use a Select element that we populate (Data passed from PHP to JS var ideal, or just render all options)
                 
                 // Using the global variable defined in PH P view: categoryData
                 let options = '<option value="">-- Chọn Danh Mục --</option>';
                 if (typeof categoryData !== 'undefined') {
                     categoryData.forEach(cat => {
                        options += `<option value="${cat.slug}">${cat.name}</option>`;
                     });
                 }
                 
                 const selectHTML = `<select class="form-control" onchange="generateUrl('${prefix}', 'category', this.value)">${options}</select>`;
                 specificSelect.innerHTML = selectHTML;
            }
            else if (type === 'product') {
                let options = '<option value="">-- Chọn Món (Signature) --</option>';
                if (typeof productData !== 'undefined') {
                    productData.forEach(p => {
                        options += `<option value="${p.id}">${p.name}</option>`;
                    });
                }
                const selectHTML = `<select class="form-control" onchange="generateUrl('${prefix}', 'product', this.value)">${options}</select>`;
                specificSelect.innerHTML = selectHTML;
            }
            else if (type === 'search') {
                specificSelect.innerHTML = `<input type="text" class="form-control" placeholder="Nhập từ khóa (e.g. healthy)" onchange="generateUrl('${prefix}', 'search', this.value)">`;
            }
        });
    }

    window.generateUrl = function(prefix, type, value) {
        let finalUrl = '';
        if (type === 'category') {
            finalUrl = `index.php?page=menu&category=${value}`;
        } else if (type === 'product') {
            finalUrl = `index.php?page=product_detail&id=${value}`;
        } else if (type === 'search') {
            finalUrl = `index.php?page=menu&search=${encodeURIComponent(value)}`; // Map search to menu filter usually
        }
        document.getElementById(prefix + 'url').value = finalUrl;
    }

    setupUrlGenerator('add_');
    setupUrlGenerator('edit_');


    // --- Edit Handle ---
    window.openEditModal = function(tag) {
        // Populate fields
        document.getElementById('edit_id').value = tag.id;
        document.getElementById('edit_name').value = tag.name;
        document.getElementById('edit_icon').value = tag.icon;
        document.getElementById('edit_url').value = tag.url;
        document.getElementById('edit_sort_order').value = tag.sort_order;
        
        editModal.style.display = 'flex';
    }

    // --- Delete Handle ---
    window.confirmDelete = function(id) {
        if(confirm('Bạn có chắc chắn muốn xóa Tag này không?')) {
            window.location.href = `tags.php?action=delete&id=${id}`;
        }
    }
});

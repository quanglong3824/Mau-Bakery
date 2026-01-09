function openReviewModal(pid, pname) {
    document.getElementById('reviewModal').style.display = 'flex';
    document.getElementById('reviewProductId').value = pid;
    document.getElementById('reviewProductName').innerText = pname;
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

function cancelOrder() {
     alert('Tính năng hủy đang phát triển');
}

window.onclick = function (event) {
    var modal = document.getElementById('reviewModal');
    if (event.target == modal) {
        closeReviewModal();
    }
}

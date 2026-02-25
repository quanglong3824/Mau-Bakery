function openReviewModal(pid, pname) {
    document.getElementById('reviewModal').style.display = 'flex';
    document.getElementById('reviewProductId').value = pid;
    document.getElementById('reviewProductName').innerText = pname;
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

function openFirstUnreviewedModal() {
    // Find the first button that triggers openReviewModal
    const reviewBtns = document.querySelectorAll('button[onclick^="openReviewModal"]');
    if (reviewBtns.length > 0) {
        reviewBtns[0].click();
    } else {
        alert('Bạn đã đánh giá tất cả sản phẩm trong đơn hàng này! Xin cảm ơn.');
    }
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

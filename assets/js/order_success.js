function checkOrder() {
    const orderCode = prompt("Vui lòng nhập mã đơn hàng của bạn (Ví dụ: ORD-...):");
    if (orderCode && orderCode.trim() !== "") {
        window.location.href = "index.php?page=order_detail&code=" + encodeURIComponent(orderCode.trim());
    }
}

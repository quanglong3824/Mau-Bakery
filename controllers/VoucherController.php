<?php
// controllers/VoucherController.php

// Fetch active coupons
$vouchers = [];
if (isset($conn)) {
    try {
        $stmt_v = $conn->prepare("SELECT * FROM coupons WHERE is_active = 1 AND expiry_date >= NOW() AND quantity > 0 ORDER BY expiry_date ASC");
        $stmt_v->execute();
        $raw_vouchers = $stmt_v->fetchAll();

        // Process for display
        $colors = ['#FFD1DC', '#B19CD9', '#FFF9E3', '#E0F2FE', '#DCFCE7'];
        foreach ($raw_vouchers as $index => $v) {
            $discount_display = ($v['discount_type'] == 'percent') ? intval($v['discount_value']) . '%' : number_format($v['discount_value'], 0, ',', '.') . 'đ';

            $vouchers[] = [
                'code' => $v['code'],
                'discount' => $discount_display,
                'min_spend' => number_format($v['min_order'], 0, ',', '.') . 'đ',
                'expiry' => date('d/m/Y', strtotime($v['expiry_date'])),
                'color' => $colors[$index % count($colors)],
                'desc' => ($v['discount_type'] == 'percent') ? "Giảm $discount_display cho đơn hàng." : "Giảm trực tiếp $discount_display."
            ];
        }

    } catch (PDOException $e) {
        // Handle error or leave empty
    }
}

// Fetch Promotions (Posts with category 'Tin tức' or 'Khuyến mãi')
$promotions = [];
if (isset($conn)) {
    try {
        $stmt_p = $conn->prepare("SELECT * FROM posts WHERE is_active = 1 AND (category = 'Tin tức' OR category = 'Khuyến mãi') ORDER BY created_at DESC LIMIT 3");
        $stmt_p->execute();
        $raw_promos = $stmt_p->fetchAll();

        foreach ($raw_promos as $p) {
            $promotions[] = [
                'title' => $p['title'],
                'desc' => mb_substr(strip_tags($p['content']), 0, 100) . '...',
                'image' => $p['image'],
                'link' => 'index.php?page=blog_detail&id=' . $p['id']
            ];
        }
    } catch (PDOException $e) {
        // Handle error
    }
}
?>
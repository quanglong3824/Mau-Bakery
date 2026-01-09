<?php
// controllers/FaqController.php

$faqs_grouped = [];

if (isset($conn)) {
    try {
        // Fetch FAQs ordered by category and sort order
        $stmt = $conn->prepare("SELECT * FROM faqs ORDER BY category, sort_order ASC");
        $stmt->execute();
        $all_faqs = $stmt->fetchAll();

        // Group by category
        foreach ($all_faqs as $faq) {
            $cat = $faq['category']; // e.g., 'delivery', 'product', 'payment'

            // Map keys to readable titles
            $cat_titles = [
                'delivery' => 'Giao Hàng & Vận Chuyển',
                'product' => 'Sản Phẩm & Bảo Quản',
                'payment' => 'Thanh Toán & Đổi Trả',
                'general' => 'Câu Hỏi Chung'
            ];
            $title = $cat_titles[$cat] ?? ucfirst($cat);

            if (!isset($faqs_grouped[$cat])) {
                $faqs_grouped[$cat] = [
                    'title' => $title,
                    'items' => []
                ];
            }
            $faqs_grouped[$cat]['items'][] = [
                'q' => $faq['question'],
                'a' => $faq['answer']
            ];
        }
    } catch (PDOException $e) {
        // Fallback or specific error handling
    }
}
?>
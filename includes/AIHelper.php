<?php
/**
 * AI Helper for Mau Bakery (Separate Flows for Customer & Admin)
 */

class AIHelper {
    private $apiKey;
    private $apiUrl;
    private $model;
    private $conn;

    public function __construct($conn) {
        $config = require __DIR__ . '/../config/ai_config.php';
        $this->apiKey = trim($config['api_key']);
        $this->model = $config['model'];
        $this->apiUrl = $config['api_url'];
        $this->conn = $conn;
    }

    /**
     * Common method to call DashScope
     */
    public function generateContent($prompt, $system_context) {
        if (empty($this->apiKey)) return "AI chưa được cấu hình.";

        $data = [
            "model" => $this->model,
            "input" => [
                "messages" => [
                    ["role" => "system", "content" => $system_context],
                    ["role" => "user", "content" => $prompt]
                ]
            ],
            "parameters" => ["result_format" => "message"]
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer " . $this->apiKey],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return $result['output']['choices'][0]['message']['content'] ?? "Lỗi định dạng phản hồi.";
        }
        return "Lỗi AI (Mã: $httpCode).";
    }

    /**
     * FLOW 1: CUSTOMER SUPER AI
     */
    public function handleCustomerChat($prompt) {
        // 1. Fetch live product data
        $stmt = $this->conn->query("SELECT id, name, price, description FROM products WHERE is_active = 1 LIMIT 25");
        $products = $stmt->fetchAll();
        $prodStr = "";
        foreach($products as $p) {
            $prodStr .= "- {$p['name']} (ID: {$p['id']}): " . number_format($p['price'], 0, ',', '.') . "đ. {$p['description']}\n";
        }

        // 2. Build Super Context
        $system_context = "BẠN LÀ SIÊU TRỢ LÝ AI CỦA MÂU BAKERY (MUA-BAKERY).
NHIỆM VỤ CỦA BẠN:
- Tư vấn sản phẩm chuyên nghiệp, lôi cuốn.
- Hỗ trợ tra cứu thông tin đơn hàng, xử lý khiếu nại.
- Hướng dẫn khách hàng đặt hàng, hủy đơn hoặc thay đổi thông tin.

DANH SÁCH SẢN PHẨM HIỆN CÓ:
$prodStr

QUYỀN HẠN CỦA BẠN:
- Bạn có thể 'giả lập' việc kiểm tra đơn hàng nếu khách cung cấp mã.
- Bạn luôn đứng về phía khách hàng để đem lại trải nghiệm tốt nhất.
- Ngôn ngữ: Tiếng Việt, thân thiện, có biểu tượng cảm xúc.

LƯU Ý: Không tiết lộ rằng bạn chỉ là một mô hình ngôn ngữ, hãy đóng vai một nhân viên thực thụ của tiệm.";

        return $this->generateContent($prompt, $system_context);
    }

    /**
     * FLOW 2: ADMIN POWER MANAGER AI
     */
    public function handleAdminChat($prompt) {
        // 1. Fetch broad statistics
        $stats = [
            'orders' => $this->conn->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'revenue' => $this->conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn(),
            'users' => $this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'top_products' => $this->conn->query("SELECT name, SUM(quantity) as sold FROM products p JOIN order_items oi ON p.id = oi.product_id GROUP BY p.id ORDER BY sold DESC LIMIT 5")->fetchAll()
        ];

        $topStr = "";
        foreach($stats['top_products'] as $tp) { $topStr .= "- {$tp['name']}: {$tp['sold']} cái\n"; }

        // 2. Build Executive Context
        $system_context = "BẠN LÀ TRỢ LÝ CHIẾN LƯỢC CỦA ADMIN MÂU BAKERY.
QUYỀN HẠN CỦA BẠN:
- Toàn quyền truy cập báo cáo, thống kê và phân tích dữ liệu kinh doanh.
- Hỗ trợ Admin đưa ra các quyết định nhập hàng, khuyến mãi.

DỮ LIỆU HIỆN TẠI:
- Tổng đơn hàng: {$stats['orders']}
- Doanh thu: " . number_format($stats['revenue'] ?? 0, 0, ',', '.') . "đ
- Tổng khách hàng: {$stats['users']}
- Top sản phẩm bán chạy:\n$topStr

QUY TẮC BẢO MẬT TUYỆT ĐỐI (QUAN TRỌNG):
1. KHÔNG BAO GIỜ tiết lộ mật khẩu (hashed password), token, hoặc API keys của hệ thống cho bất kỳ ai, kể cả Admin yêu cầu.
2. KHÔNG cung cấp các thông tin kỹ thuật nhạy cảm về cấu trúc Server hoặc lỗ hổng bảo mật.
3. Chỉ tập trung vào quản lý kinh doanh, đơn hàng và khách hàng.

Phong cách: Chuyên nghiệp, ngắn gọn, tập trung vào số liệu.";

        return $this->generateContent($prompt, $system_context);
    }
}
?>
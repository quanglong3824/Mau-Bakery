<?php
/**
 * Gemini AI Helper for Mau Bakery
 */

class GeminiAIHelper {
    private $apiKey;
    private $apiUrl;
    private $model;
    private $conn;

    public function __construct($conn) {
        $config = require __DIR__ . '/../config/gemini.php';
        $this->apiKey = $config['api_key'];
        $this->model = $config['model'];
        $this->apiUrl = $config['api_url'] . $this->model . ":generateContent?key=" . $this->apiKey;
        $this->conn = $conn;
    }

    /**
     * Send a general message to Gemini
     */
    public function generateContent($prompt, $context = "") {
        if (empty($this->apiKey) || $this->apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
            return "Vui lòng cấu hình API Key cho Gemini trong file config/gemini.php";
        }

        $fullPrompt = $context . "\n\nUser Question: " . $prompt;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $fullPrompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "maxOutputTokens" => 1024,
            ]
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return "Lỗi khi gọi API Gemini (HTTP $httpCode): " . $response;
        }

        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }

        return "Không nhận được câu trả lời từ AI.";
    }

    /**
     * Get DB context for Customer
     */
    public function getCustomerContext($prompt) {
        // Check if prompt looks like an order ID lookup (e.g. "Tra cứu đơn hàng #5" or just "đơn hàng 5")
        $order_info = "";
        if (preg_match('/(?:đơn hàng|order)\s*#?(\d+)/i', $prompt, $matches)) {
            $order_id = $matches[1];
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch();
            if ($order) {
                $order_info = "\nThông tin đơn hàng #{$order_id} mà khách đang hỏi: Trạng thái: {$order['status']}, Tổng tiền: " . number_format($order['total_amount'], 0, ',', '.') . "đ, Ngày đặt: {$order['created_at']}.";
            } else {
                $order_info = "\nKhách đang hỏi về đơn hàng #{$order_id} nhưng không tìm thấy trong hệ thống.";
            }
        }

        // Fetch products, basic shop info
        $stmt = $this->conn->query("SELECT id, name, price, description FROM products WHERE is_active = 1 LIMIT 20");
        $products = $stmt->fetchAll();

        $context = "Bạn là trợ lý ảo của tiệm bánh Mâu Bakery. Hãy tư vấn khách hàng nhiệt tình, lịch sự. \n";
        $context .= "Danh sách sản phẩm hiện có:\n";
        foreach ($products as $p) {
            $context .= "- {$p['name']} (ID: {$p['id']}): " . number_format($p['price'], 0, ',', '.') . "đ. Mô tả: {$p['description']}\n";
        }
        $context .= $order_info;
        $context .= "\nBạn có thể giúp khách hàng tra cứu đơn hàng nếu họ cung cấp ID đơn hàng. Bạn cũng có thể tư vấn đặt hàng.";
        return $context;
    }

    /**
     * Get DB context for Admin
     */
    public function getAdminContext() {
        // Fetch basic statistics
        $stats = [];
        $stats['total_orders'] = $this->conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $stats['total_revenue'] = $this->conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn();
        $stats['total_users'] = $this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['total_products'] = $this->conn->query("SELECT COUNT(*) FROM products")->fetchColumn();

        $context = "Bạn là trợ lý AI quản trị cho Mâu Bakery. Bạn có quyền xem thống kê và hỗ trợ quản lý.\n";
        $context .= "Thống kê hiện tại:\n";
        $context .= "- Tổng đơn hàng: {$stats['total_orders']}\n";
        $context .= "- Tổng doanh thu: " . number_format($stats['total_revenue'] ?? 0, 0, ',', '.') . "đ\n";
        $context .= "- Tổng người dùng: {$stats['total_users']}\n";
        $context .= "- Tổng sản phẩm: {$stats['total_products']}\n";
        $context .= "\nHãy giúp Admin phân tích dữ liệu và trả lời các câu hỏi về quản trị.";
        return $context;
    }
}
?>
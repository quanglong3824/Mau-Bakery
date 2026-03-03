<?php
/**
 * AI Helper for Mau Bakery (Using Alibaba DashScope / OpenAI Protocol)
 */

class AIHelper {
    private $apiKey;
    private $apiUrl;
    private $model;
    private $conn;

    public function __construct($conn) {
        $config = require __DIR__ . '/../config/ai_config.php';
        $this->apiKey = $config['api_key'];
        $this->model = $config['model'];
        $this->apiUrl = $config['api_url'];
        $this->conn = $conn;
    }

    /**
     * Send a general message to AI (DashScope / OpenAI)
     */
    public function generateContent($prompt, $system_context = "") {
        if (empty($this->apiKey) || $this->apiKey === 'YOUR_API_KEY_HERE') {
            return "Vui lòng cấu hình API Key trong file config/ai_config.php";
        }

        $messages = [
            ["role" => "system", "content" => $system_context],
            ["role" => "user", "content" => $prompt]
        ];

        $data = [
            "model" => $this->model,
            "messages" => $messages,
            "temperature" => 0.7,
            "max_tokens" => 1024
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Cấu hình Header tối ưu cho Alibaba International
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey, // Chuẩn OpenAI
            'dashscope-api-key: ' . $this->apiKey,    // Chuẩn riêng của Alibaba (đôi khi bắt buộc)
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return "Lỗi khi gọi API (HTTP $httpCode): " . $response;
        }

        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            return $result['choices'][0]['message']['content'];
        }

        return "Không nhận được câu trả lời từ AI.";
    }

    /**
     * Get DB context for Customer
     */
    public function getCustomerContext($prompt) {
        $order_info = "";
        if (preg_match('/(?:đơn hàng|order)\s*#?(\d+)/i', $prompt, $matches)) {
            $order_id = $matches[1];
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch();
            if ($order) {
                $order_info = "
Thông tin đơn hàng #{$order_id} mà khách đang hỏi: Trạng thái: {$order['status']}, Tổng tiền: " . number_format($order['total_amount'], 0, ',', '.') . "đ, Ngày đặt: {$order['created_at']}.";
            } else {
                $order_info = "
Khách đang hỏi về đơn hàng #{$order_id} nhưng không tìm thấy trong hệ thống.";
            }
        }

        $stmt = $this->conn->query("SELECT id, name, price, description FROM products WHERE is_active = 1 LIMIT 20");
        $products = $stmt->fetchAll();

        $context = "Bạn là trợ lý ảo của tiệm bánh Mâu Bakery. Hãy tư vấn khách hàng nhiệt tình, lịch sự. 
";
        $context .= "Danh sách sản phẩm hiện có:
";
        foreach ($products as $p) {
            $context .= "- {$p['name']} (ID: {$p['id']}): " . number_format($p['price'], 0, ',', '.') . "đ. Mô tả: {$p['description']}
";
        }
        $context .= $order_info;
        $context .= "
Bạn có thể giúp khách hàng tra cứu đơn hàng nếu họ cung cấp ID đơn hàng. Bạn cũng có thể tư vấn đặt hàng.";
        return $context;
    }

    /**
     * Get DB context for Admin
     */
    public function getAdminContext() {
        $stats = [];
        $stats['total_orders'] = $this->conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $stats['total_revenue'] = $this->conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn();
        $stats['total_users'] = $this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['total_products'] = $this->conn->query("SELECT COUNT(*) FROM products")->fetchColumn();

        $context = "Bạn là trợ lý AI quản trị cho Mâu Bakery. Bạn có quyền xem thống kê và hỗ trợ quản lý.
";
        $context .= "Thống kê hiện tại:
";
        $context .= "- Tổng đơn hàng: {$stats['total_orders']}
";
        $context .= "- Tổng doanh thu: " . number_format($stats['total_revenue'] ?? 0, 0, ',', '.') . "đ
";
        $context .= "- Tổng người dùng: {$stats['total_users']}
";
        $context .= "- Tổng sản phẩm: {$stats['total_products']}
";
        $context .= "
Hãy giúp Admin phân tích dữ liệu và trả lời các câu hỏi về quản trị.";
        return $context;
    }
}
?>
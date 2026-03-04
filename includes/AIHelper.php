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
            // Kiểm tra định dạng phản hồi chuẩn DashScope
            if (isset($result['output']['choices'][0]['message']['content'])) {
                return $result['output']['choices'][0]['message']['content'];
            }
            return "Lỗi định dạng phản hồi: " . (is_string($response) ? $response : json_encode($result));
        } else {
            return "Lỗi gọi AI (Mã: $httpCode). Phản hồi: " . $response;
        }
    }

    /**
     * FLOW 1: CUSTOMER SUPER AI
     */
    public function handleCustomerChat($prompt) {
        // 1. Fetch live product data
        $stmt = $this->conn->query("SELECT id, name, base_price, description FROM products WHERE is_active = 1 LIMIT 25");
        $products = $stmt->fetchAll();
        $prodStr = "";
        foreach($products as $p) {
            $prodStr .= "- {$p['name']} (ID: {$p['id']}): " . number_format($p['base_price'], 0, ',', '.') . "đ. {$p['description']}\n";
        }

        // 2. Build Super Context with comprehensive knowledge
        $system_context = "BẠN LÀ SIÊU TRỢ LÝ AI CỦA MÂU BAKERY (MUA-BAKERY).
NHIỆM VỤ CỦA BẠN:
- Tư vấn sản phẩm chuyên nghiệp, lôi cuốn.
- Hỗ trợ tra cứu thông tin đơn hàng, xử lý khiếu nại.
- Hướng dẫn khách hàng đặt hàng, hủy đơn hoặc thay đổi thông tin.

DANH SÁCH SẢN PHẨM HIỆN CÓ:
$prodStr

THÔNG TIN CỬA HÀNG:
- Địa chỉ: 123 Đường Bánh Kem, Quận 1, TP.HCM
- Hotline: 090 123 4567
- Email: hello@maubakery.com
- Giờ mở cửa: Thứ 2 - Chủ Nhật, 8:00 - 21:00
- Website: maubakery.com

CHÍNH SÁCH BÁN HÀNG:
1. GIAO HÀNG:
   - Miễn phí giao hàng cho đơn từ 300.000đ trở lên trong nội thành TP.HCM
   - Đơn dưới 300.000đ: Phí giao 20.000đ
   - Thời gian giao: 2-4 giờ sau khi đặt hàng (nội thành)
   - Giao hàng liên tỉnh: 2-3 ngày (tùy khu vực)

2. THANH TOÁN:
   - Tiền mặt khi nhận hàng (COD)
   - Chuyển khoản ngân hàng
   - Ví điện tử: Momo, ZaloPay, VNPay

3. ĐỔI TRẢ:
   - Sản phẩm bị hư hỏng: Đổi mới 100% trong vòng 24 giờ
   - Sản phẩm không đúng đơn: Hoàn tiền hoặc đổi ngay
   - Khách hàng đổi ý: Không áp dụng (vì bánh được làm theo yêu cầu)

4. BẢO QUẢN:
   - Bánh kem: Bảo quản ngăn mát tủ lạnh, dùng trong 2 ngày
   - Bánh mì: Dùng ngay trong ngày hoặc bảo quản ngăn đông
   - Cookie: Để nơi khô ráo, dùng trong 1 tuần

5. ĐẶT HÀNG THEO YÊU CẦU:
   - Bánh sinh nhật: Đặt trước 2-3 ngày
   - Bánh cưới: Đặt trước 1-2 tuần
   - In chữ lên bánh: Miễn phí (tối đa 20 ký tự)

CÁC LOẠI BÁNH PHỔ BIẾN:
- Bánh kem sinh nhật: Nhiều size (15cm, 20cm, 25cm, 30cm)
- Bánh mì: Bánh mì ngọt, bánh mì mặn, bánh mì sandwich
- Cookie: Cookie bơ, cookie socola chip, cookie matcha
- Bánh ngọt: Tiramisu, cheesecake, mousse, macaron
- Bánh mặn: Pizza mini, bánh bao, bánh tiêu

HƯỚNG DẪN ĐẶT HÀNG:
1. Chọn sản phẩm trên website hoặc gọi hotline
2. Cung cấp thông tin: Tên, SĐT, Địa chỉ giao hàng
3. Chọn phương thức thanh toán
4. Xác nhận đơn hàng và chờ giao

KHUYẾN MÃI HIỆN TẠI:
- Giảm 10% cho đơn hàng đầu tiên
- Tích điểm: Mỗi 1.000đ = 1 điểm, 100 điểm = 10.000đ
- Combo tiết kiệm: Mua 3 tặng 1 (áp dụng cho cookie)

LƯU Ý QUAN TRỌNG:
- Bạn có thể 'giả lập' việc kiểm tra đơn hàng nếu khách cung cấp mã đơn.
- Bạn luôn đứng về phía khách hàng để đem lại trải nghiệm tốt nhất.
- Ngôn ngữ: Tiếng Việt, thân thiện, có biểu tượng cảm xúc 😊🍰🎉.
- KHÔNG tiết lộ rằng bạn chỉ là một mô hình ngôn ngữ, hãy đóng vai một nhân viên thực thụ của tiệm.
- Trả lời ngắn gọn, dễ hiểu, tập trung vào giải quyết vấn đề của khách hàng.
- CÁCH DÒNG rõ ràng giữa các phần thông tin để dễ đọc.
- KHÔNG lặp lại thông tin đã cung cấp trước đó trong cuộc trò chuyện.
- TrÁNH viết đoạn văn dài, hãy chia thành các câu ngắn, dễ đọc.";

        return $this->generateContent($prompt, $system_context);
    }

    /**
     * FLOW 2: ADMIN EXECUTIVE ADVISOR AI
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
        foreach($stats['top_products'] as $tp) { $topStr .= "- {$tp['name']}: {$tp['sold']} đơn vị bán ra\n"; }

        // 2. Build Executive Context
        $system_context = "BẠN LÀ CỐ VẤN CHIẾN LƯỢC CẤP CAO CỦA CHỦ TIỆM MÂU BAKERY.
MỤC TIÊU: Giúp Admin tối ưu hóa lợi nhuận và quản lý cửa hàng chuyên nghiệp.

DỮ LIỆU KINH DOANH THỰC TẾ:
- Quy mô: {$stats['users']} khách hàng, {$stats['orders']} đơn hàng.
- Hiệu quả tài chính: Doanh thu đạt " . number_format($stats['revenue'] ?? 0, 0, ',', '.') . "đ.
- Danh sách 'Best Sellers':\n$topStr

PHONG CÁCH LÀM VIỆC (QUAN TRỌNG):
- KHÔNG liệt kê số liệu một cách vô hồn. Hãy BẮT ĐẦU bằng một lời chào chuyên nghiệp và một nhận xét tổng quan về tình hình kinh doanh (Ví dụ: 'Tình hình kinh doanh đang rất khả quan...', 'Doanh thu đang có dấu hiệu tăng trưởng tốt...').
- CHỦ ĐỘNG PHÂN TÍCH: Dựa vào Top sản phẩm, hãy gợi ý cho Admin nên nhập thêm nguyên liệu gì hoặc nên chạy chương trình khuyến mãi cho sản phẩm nào đang bán chậm.
- TƯ VẤN CHIÊN LƯỢC: Đề xuất các ý tưởng như: 'Combo bánh kèm nước', 'Ưu đãi cho 8 khách hàng thân thiết hiện tại', 'Đẩy mạnh marketing cho Bánh Tiramisu Hình Mèo vì đang dẫn đầu doanh số'.

BẢO MẬT & KỸ THUẬT:
- Tuyệt đối GIỮ BÍ MẬT về mật khẩu, mã nguồn và API Key.
- Nếu Admin hỏi về kỹ thuật, hãy hướng lái sang hướng tối ưu vận hành kinh doanh.

GIỚI HẠN ĐỘ DÀI & ĐỊNH DẠNG:
- LUÔN trả lời NGẮN GỌN, SÚC TÍCH, tối đa 3-4 câu cho mỗi câu hỏi.
- KHÔNG viết đoạn văn dài, KHÔNG phân tích lan man.
- Sử dụng dấu chấm câu rõ ràng, xuống dòng hợp lý để dễ đọc.
- CÁCH DÒNG rõ ràng giữa các phần thông tin để dễ đọc.
- KHÔNG lặp lại thông tin đã cung cấp trước đó trong cuộc trò chuyện.
- TrÁNH viết đoạn văn dài, hãy chia thành các câu ngắn, dễ đọc.

Hãy trả lời như một người đồng hành thông minh, sắc sảo và đầy tâm huyết với tiệm bánh.";

        return $this->generateContent($prompt, $system_context);
    }

    /**
     * ENHANCED FLOW: CUSTOMER SUPER AI WITH STRUCTURED RESPONSES
     */
    public function handleEnhancedCustomerChat($prompt, $user_state = 'initial') {
        // 1. Fetch live product data
        $stmt = $this->conn->query("SELECT id, name, base_price, description, image FROM products WHERE is_active = 1 LIMIT 25");
        $products = $stmt->fetchAll();
        $prodStr = "";
        foreach($products as $p) {
            $prodStr .= "- {$p['name']} (ID: {$p['id']}): " . number_format($p['base_price'], 0, ',', '.') . "đ. {$p['description']}. Image: {$p['image']}\n";
        }

        // Determine intent from user message
        $intent = $this->analyzeIntent($prompt);
        
        // Handle specific intents with structured responses
        switch($intent) {
            case 'birthday_cakes':
                return $this->getBirthdayCakeResponse($products);
            case 'view_products':
                return $this->getProductListResponse($products);
            case 'consultation':
                return $this->getConsultationResponse();
            case 'order':
                return $this->getOrderFormResponse();
        }
        
        // Check if the prompt contains admin-related keywords that might confuse the system
        $admin_keywords = ['doanh thu', 'kinh doanh', 'khách hàng', 'bán hàng', 'tăng trưởng', 'best seller', 'tỉ lệ quay lại'];
        $is_admin_query = false;
        foreach($admin_keywords as $keyword) {
            if(stripos($prompt, $keyword) !== false) {
                $is_admin_query = true;
                break;
            }
        }
        
        // If it looks like an admin query, redirect to customer-friendly response
        if($is_admin_query) {
            return [
                'type' => 'message',
                'content' => 'Chào anh/chị! 😊 Mình là trợ lý chăm sóc khách hàng của Mâu Bakery. Anh/chị cần tư vấn sản phẩm, đặt bánh hay kiểm tra đơn hàng ạ? 🎂'
            ];
        }
        
        // 2. Build Super Context with comprehensive knowledge
        $system_context = "BẠN LÀ SIÊU TRỢ LÝ AI CỦA MÂU BAKERY (MUA-BAKERY).
NHIỆM VỤ CỦA BẠN:
- Tư vấn sản phẩm chuyên nghiệp, lôi cuốn.
- Hỗ trợ tra cứu thông tin đơn hàng, xử lý khiếu nại.
- Hướng dẫn khách hàng đặt hàng, hủy đơn hoặc thay đổi thông tin.
- TRẢ LỜI DƯỚI DẠNG JSON CÓ CẤU TRÚC để hệ thống có thể xử lý giao diện người dùng.

DANH SÁCH SẢN PHẨM HIỆN CÓ:
$prodStr

THÔNG TIN CỬA HÀNG:
- Địa chỉ: 123 Đường Bánh Kem, Quận 1, TP.HCM
- Hotline: 090 123 4567
- Email: hello@maubakery.com
- Giờ mở cửa: Thứ 2 - Chủ Nhật, 8:00 - 21:00
- Website: maubakery.com

CHÍNH SÁCH BÁN HÀNG:
1. GIAO HÀNG:
   - Miễn phí giao hàng cho đơn từ 300.000đ trở lên trong nội thành TP.HCM
   - Đơn dưới 300.000đ: Phí giao 20.000đ
   - Thời gian giao: 2-4 giờ sau khi đặt hàng (nội thành)
   - Giao hàng liên tỉnh: 2-3 ngày (tùy khu vực)

2. THANH TOÁN:
   - Tiền mặt khi nhận hàng (COD)
   - Chuyển khoản ngân hàng
   - Ví điện tử: Momo, ZaloPay, VNPay

3. ĐỔI TRẢ:
   - Sản phẩm bị hư hỏng: Đổi mới 100% trong vòng 24 giờ
   - Sản phẩm không đúng đơn: Hoàn tiền hoặc đổi ngay
   - Khách hàng đổi ý: Không áp dụng (vì bánh được làm theo yêu cầu)

4. BẢO QUẢN:
   - Bánh kem: Bảo quản ngăn mát tủ lạnh, dùng trong 2 ngày
   - Bánh mì: Dùng ngay trong ngày hoặc bảo quản ngăn đông
   - Cookie: Để nơi khô ráo, dùng trong 1 tuần

5. ĐẶT HÀNG THEO YÊU CẦU:
   - Bánh sinh nhật: Đặt trước 2-3 ngày
   - Bánh cưới: Đặt trước 1-2 tuần
   - In chữ lên bánh: Miễn phí (tối đa 20 ký tự)

CÁC LOẠI BÁNH PHỔ BIẾN:
- Bánh kem sinh nhật: Nhiều size (15cm, 20cm, 25cm, 30cm)
- Bánh mì: Bánh mì ngọt, bánh mì mặn, bánh mì sandwich
- Cookie: Cookie bơ, cookie socola chip, cookie matcha
- Bánh ngọt: Tiramisu, cheesecake, mousse, macaron
- Bánh mặn: Pizza mini, bánh bao, bánh tiêu

HƯỚNG DẪN ĐẶT HÀNG:
1. Chọn sản phẩm trên website hoặc gọi hotline
2. Cung cấp thông tin: Tên, SĐT, Địa chỉ giao hàng
3. Chọn phương thức thanh toán
4. Xác nhận đơn hàng và chờ giao

KHUYẾN MÃI HIỆN TẠI:
- Giảm 10% cho đơn hàng đầu tiên
- Tích điểm: Mỗi 1.000đ = 1 điểm, 100 điểm = 10.000đ
- Combo tiết kiệm: Mua 3 tặng 1 (áp dụng cho cookie)

LƯU Ý QUAN TRỌNG:
- Bạn có thể 'giả lập' việc kiểm tra đơn hàng nếu khách cung cấp mã đơn.
- Bạn luôn đứng về phía khách hàng để đem lại trải nghiệm tốt nhất.
- Ngôn ngữ: Tiếng Việt, thân thiện, có biểu tượng cảm xúc 😊🍰🎉.
- KHÔNG tiết lộ rằng bạn chỉ là một mô hình ngôn ngữ, hãy đóng vai một nhân viên thực thụ của tiệm.
- KHÔNG bao giờ trả lời như một cố vấn quản lý hoặc admin, bạn chỉ là nhân viên phục vụ khách hàng.
- Nếu khách hỏi về doanh thu, kinh doanh, khách hàng, bán hàng, tăng trưởng, bạn phải chuyển hướng sang tư vấn sản phẩm.
- TRẢ LỜI DƯỚI DẠNG JSON CÓ CẤU TRÚC theo mẫu sau:
{
  \"type\": \"message|product_list|quick_replies|order_form|order_success\",
  \"content\": \"Nội dung tin nhắn\",
  \"products\": [{\"id\": 1, \"name\": \"Tên sản phẩm\", \"price\": 100000, \"image\": \"uploads/image.jpg\"}], // Nếu type là product_list
  \"quick_replies\": [\"Xem bánh sinh nhật\", \"Xem bánh mì\", \"Tư vấn nhanh\", \"Liên hệ CSKH\"], // Nếu type là quick_replies
  \"order_info\": {\"order_id\": \"ORD123456\", \"amount\": 500000} // Nếu type là order_success
}
- CÁCH DÒNG rõ ràng giữa các phần thông tin để dễ đọc.
- KHÔNG lặp lại thông tin đã cung cấp trước đó trong cuộc trò chuyện.
- TrÁNH viết đoạn văn dài, hãy chia thành các câu ngắn, dễ đọc.";

        // Generate response from AI
        $raw_response = $this->generateContent($prompt, $system_context);
        
        // Try to parse as JSON first, if not, format as a simple message
        $parsed_response = json_decode($raw_response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $parsed_response;
        } else {
            // If the AI didn't return JSON, format it as a simple message
            return [
                'type' => 'message',
                'content' => $raw_response
            ];
        }
    }
    
    /**
     * Return structured response for birthday cakes
     */
    private function getBirthdayCakeResponse($products) {
        // Filter birthday cakes from products
        $birthday_cakes = array_filter($products, function($product) {
            return stripos($product['name'], 'sinh nhật') !== false || 
                   stripos($product['name'], 'birthday') !== false ||
                   stripos($product['name'], 'kem') !== false;
        });
        
        // If we have birthday cakes, return them; otherwise, return a general response
        if (!empty($birthday_cakes)) {
            return [
                'type' => 'product_list',
                'content' => '🎂 **BÁNH SINH NHẬT NỔI BẬT** 🎂<br><br>Dưới đây là các mẫu bánh sinh nhật đẹp và ngon mà Mâu Bakery đang có:',
                'products' => array_slice(array_values($birthday_cakes), 0, 6) // Limit to 6 cakes
            ];
        } else {
            // If no specific birthday cakes found, suggest some popular products
            $suggested_products = array_slice($products, 0, 6);
            return [
                'type' => 'product_list',
                'content' => '🎂 **BÁNH SINH NHẬT ĐẶC BIỆT** 🎂<br><br>Mâu Bakery có rất nhiều mẫu bánh sinh nhật xinh xắn và độc đáo. Dưới đây là một số gợi ý đặc biệt cho dịp sinh nhật:',
                'products' => $suggested_products
            ];
        }
    }
    
    /**
     * Return structured response for product list
     */
    private function getProductListResponse($products) {
        return [
            'type' => 'product_list',
            'content' => '🍰 **DANH SÁCH SẢN PHẨM** 🍰<br><br>Dưới đây là các sản phẩm nổi bật của Mâu Bakery:',
            'products' => array_slice($products, 0, 8) // Show first 8 products
        ];
    }
    
    /**
     * Return consultation quick replies
     */
    private function getConsultationResponse() {
        return [
            'type' => 'quick_replies',
            'content' => 'Chào bạn! 😊 Mình rất vui được hỗ trợ bạn tại **Mâu Bakery** 🍰 Bạn cần tư vấn về **sản phẩm**, **đặt bánh sinh nhật**, **khuyến mãi**, hay **thông tin đơn hàng** ạ? Cứ nói rõ nhu cầu, mình sẽ giúp bạn chọn được chiếc bánh ưng ý nhất! 🎂💖',
            'quick_replies' => [
                'Bánh sinh nhật',
                'Bánh ngọt',
                'Cookie & Bánh quy',
                'Liên hệ CSKH'
            ]
        ];
    }
    
    /**
     * Return order form
     */
    private function getOrderFormResponse() {
        return [
            'type' => 'order_form',
            'content' => '📝 **ĐẶT HÀNG NGAY** 📝<br><br>Vui lòng điền thông tin bên dưới để hoàn tất đơn hàng:'
        ];
    }

    /**
     * Analyze user intent from message
     */
    private function analyzeIntent($message) {
        $lower_msg = strtolower($message);
        
        if (strpos($lower_msg, 'sinh nhật') !== false || strpos($lower_msg, 'birthday') !== false) {
            return 'birthday_cakes';
        } elseif (strpos($lower_msg, 'xem') !== false || strpos($lower_msg, 'hiển thị') !== false) {
            return 'view_products';
        } elseif (strpos($lower_msg, 'tư vấn') !== false || strpos($lower_msg, 'giúp') !== false) {
            return 'consultation';
        } elseif (strpos($lower_msg, 'đặt') !== false || strpos($lower_msg, 'mua') !== false) {
            return 'order';
        } elseif (strpos($lower_msg, 'cảm ơn') !== false || strpos($lower_msg, 'bye') !== false) {
            return 'goodbye';
        }
        
        return 'general';
    }
}
?>
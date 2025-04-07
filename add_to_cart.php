<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['productId'];
    $userId = $_SESSION['user_id'];

    try {
        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        $cartItem = $stmt->fetch();

        if ($cartItem) {
            // Nếu sản phẩm đã có, tăng số lượng
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = :user_id AND product_id = :product_id");
        } else {
            // Nếu chưa có, thêm mới
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)");
        }
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);

        // Lấy tổng số lượng sản phẩm trong giỏ
        $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $totalItems = $stmt->fetchColumn() ?: 0;

        echo json_encode(['success' => true, 'cartCount' => $totalItems]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>

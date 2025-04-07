<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Sửa đường dẫn include (giả định db.php nằm trong ASM2/includes)
include 'includes/db.php';

// Kiểm tra kết nối cơ sở dữ liệu
if (!isset($conn)) {
    die("Lỗi: Không thể kết nối đến cơ sở dữ liệu. Vui lòng kiểm tra tệp includes/db.php.");
}

// Lấy tổng số tiền từ giỏ hàng
$stmt = $conn->prepare("SELECT p.id, c.quantity, p.price, p.category FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$total = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $display_price = floatval($row['price']);
    if ($display_price < 1000 && in_array($row['category'], ['computer', 'gaming', 'home_appliance'])) {
        $display_price *= 1000000;
    }
    $subtotal = $row['quantity'] * $display_price;
    $total += $subtotal;
}

// Thông tin thanh toán
$bank_info = "Ngân hàng: TECHCONBANK\nSố tài khoản: 1907 2194 4790 11\nTên tài khoản: NGUYEN DUC DONG";
$payment_info = "$bank_info\nSố tiền: " . number_format($total, 0, ',', '.') . " VND";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            min-height: 100vh;
            color: #fff;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .checkout-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
            white-space: pre-wrap;
        }

        img {
            width: 300px;
            height: 300px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .back-btn {
            display: inline-block;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: linear-gradient(90deg, #4ecdc4, #ff6b6b);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Thanh toán đơn hàng</h1>
        <p><?php echo htmlspecialchars($payment_info); ?></p>
        <img src="Ảnh/z6434081571969_d13cf3ab860ca98c2aa1826ec2ea1637.jpg" alt="Mã QR Thanh toán">
        <p>Quét mã QR bằng ứng dụng ngân hàng để thanh toán</p>
        <a href="pages/cart.php" class="back-btn">Quay lại giỏ hàng</a>
    </div>
</body>
</html>
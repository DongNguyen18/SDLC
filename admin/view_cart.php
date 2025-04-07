<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

include '../includes/db.php';

if (!isset($_GET['user_id'])) {
    header('Location: manage_users.php');
    exit();
}

$user_id = $_GET['user_id'];

// Lấy thông tin người dùng
$stmtUser = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$user = $stmtUser->fetch();

if (!$user) {
    echo "User not found!";
    exit();
}

// Lấy sản phẩm trong giỏ hàng của người dùng
$stmtCart = $conn->prepare("
    SELECT p.product_name, p.price, p.category, c.quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmtCart->execute([$user_id]);
$cartItems = $stmtCart->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Cart</title>
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
        }

        h1 {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
            color: #ffd700;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }

        a[href="manage_users.php"] {
            display: inline-block;
            margin-bottom: 30px;
            color: #ff6b6b;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        a[href="manage_users.php"]:hover {
            color: #4ecdc4;
            text-shadow: 0 0 10px rgba(78, 205, 196, 0.5);
        }

        table {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: center;
            color: #333;
            font-size: 16px;
        }

        th {
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        tr:nth-child(even) {
            background: rgba(241, 242, 246, 0.5);
        }

        tr:hover {
            background: rgba(78, 205, 196, 0.2);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        td[colspan="4"] {
            font-size: 18px;
            color: #777;
            padding: 20px;
        }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($user['username']); ?>'s Cart</h1>
    <a href="manage_users.php">Back to Manage Users</a>

    <table>
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Quantity</th>
        </tr>
        <?php if ($cartItems): ?>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo number_format($item['price'], 2); ?> USD</td>
                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No products in cart.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
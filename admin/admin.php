<?php
session_start();
// Kiểm tra xem admin đã đăng nhập chưa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

include '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298); /* Gradient xanh đậm */
            min-height: 100vh;
            color: #fff;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            color: #ffd700; /* Vàng nổi bật */
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }

        nav {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px); /* Hiệu ứng kính mờ */
            margin-bottom: 40px;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
            background-size: 200% 100%;
        }

        nav ul li a:hover {
            background-position: 100% 0;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.5);
        }

        .stats {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
            color: #333;
            position: relative;
            overflow: hidden;
        }

        .stats::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(120deg, rgba(255, 107, 107, 0.2), rgba(78, 205, 196, 0.2));
            transform: rotate(30deg);
            animation: shine 6s infinite linear; /* Hiệu ứng ánh sáng */
        }

        @keyframes shine {
            0% { transform: rotate(30deg) translateX(-100%); }
            100% { transform: rotate(30deg) translateX(100%); }
        }

        .stats h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #ff6b6b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats p {
            font-size: 20px;
            margin: 15px 0;
            color: #2a5298;
            font-weight: 500;
            background: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .stats p:hover {
            transform: scale(1.05);
            background: rgba(78, 205, 196, 0.2);
        }
    </style>
</head>
<body>
    <h1>Welcome, Admin <?php echo $_SESSION['username']; ?></h1>

    <!-- Menu điều hướng -->
    <nav>
        <ul>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="../index.php">View Homepage</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Thống kê nhanh -->
    <div class="stats">
        <h2>Quick Stats</h2>
        <?php
        // Đếm số lượng người dùng
        $stmt = $conn->query("SELECT COUNT(*) as total_users FROM users");
        $total_users = $stmt->fetch()['total_users'];

        // Đếm số lượng sản phẩm
        $stmt = $conn->query("SELECT COUNT(*) as total_products FROM products");
        $total_products = $stmt->fetch()['total_products'];
        ?>
        <p>Total Users: <?php echo $total_users; ?></p>
        <p>Total Products: <?php echo $total_products; ?></p>
    </div>
</body>
</html>
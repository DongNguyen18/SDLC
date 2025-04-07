<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include '../includes/db.php';

// Xử lý cập nhật số lượng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    if ($quantity > 0) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute(['quantity' => $quantity, 'user_id' => $_SESSION['user_id'], 'product_id' => $product_id]);
    } else {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute(['user_id' => $_SESSION['user_id'], 'product_id' => $product_id]);
    }
    header('Location: cart.php');
    exit();
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'product_id' => $product_id]);
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart</title>
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
            margin-bottom: 30px;
            color: #ffd700;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
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
        }

        tr:nth-child(even) {
            background: rgba(241, 242, 246, 0.5);
        }

        tr:hover {
            background: rgba(78, 205, 196, 0.2);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }

        button[type="submit"] {
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        button[type="submit"]:hover {
            background: linear-gradient(90deg, #4ecdc4, #ff6b6b);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
        }

        a.delete-btn {
            color: #ff4757;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        a.delete-btn:hover {
            background: #ff4757;
            color: #fff;
            box-shadow: 0 5px 15px rgba(255, 71, 87, 0.4);
        }

        .total-row {
            font-weight: 700;
            background: #f1f2f6;
        }

        .cart-actions {
            max-width: 1000px;
            margin: 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-link {
            color: #ff6b6b;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #4ecdc4;
            text-shadow: 0 0 10px rgba(78, 205, 196, 0.5);
        }

        .checkout-btn {
            background: linear-gradient(90deg, #28a745, #218838);
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .checkout-btn:hover {
            background: linear-gradient(90deg, #218838, #28a745);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
    </style>
</head>
<body>
    <h1>Your Cart</h1>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php
        // Lấy thêm cột category để kiểm tra danh mục
        $stmt = $conn->prepare("SELECT p.id, p.product_name, c.quantity, p.price, p.category FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = :user_id");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $total = 0;

        while ($row = $stmt->fetch()) {
            // Điều chỉnh giá nếu cần
            $display_price = floatval($row['price']);
            if ($display_price < 1000 && in_array($row['category'], ['computer', 'gaming', 'home_appliance'])) {
                $display_price = $display_price * 1000000;
            }

            $subtotal = $row['quantity'] * $display_price;
            $total += $subtotal;

            echo "<tr>
                    <td>{$row['product_name']}</td>
                    <td>
                        <form method='POST' action='cart.php'>
                            <input type='hidden' name='product_id' value='{$row['id']}'>
                            <input type='number' name='quantity' value='{$row['quantity']}' min='0'>
                            <button type='submit' name='update_quantity'>Update</button>
                        </form>
                    </td>
                    <td>" . number_format($display_price, 0, ',', '.') . " VND</td>
                    <td>" . number_format($subtotal, 0, ',', '.') . " VND</td>
                    <td><a href='cart.php?delete={$row['id']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to remove this item?\")'>Delete</a></td>
                  </tr>";
        }
        ?>
        <tr class="total-row">
            <td colspan="3">Total</td>
            <td><?php echo number_format($total, 0, ',', '.') . " VND"; ?></td>
            <td></td>
        </tr>
    </table>

    <div class="cart-actions">
        <a href="../index.php" class="back-link">Continue Shopping</a>
        <a href="../checkout.php" class="checkout-btn">Proceed to Checkout</a>
    </div>
</body>
</html>
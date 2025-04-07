<?php
session_start();
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
    <title>Manage Users</title>
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
        }

        h1 {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
            color: #ffd700; /* Vàng nổi bật */
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }

        a[href="admin.php"] {
            display: inline-block;
            margin-bottom: 30px;
            color: #ff6b6b;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        a[href="admin.php"]:hover {
            color: #4ecdc4;
            text-shadow: 0 0 10px rgba(78, 205, 196, 0.5);
        }

        table {
            width: 100%;
            max-width: 1200px;
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

        td a {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 5px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
            background-size: 200% 100%;
            color: #fff;
            transition: all 0.3s ease;
        }

        td a:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(78, 205, 196, 0.4);
        }
    </style>
</head>
<body>
    <h1>Manage Users</h1>
    <a href="admin.php">Back to Admin Dashboard</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php
        $stmt = $conn->query("SELECT id, username, email, role FROM users");
        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['role']}</td>
                    <td>
                        <a href='view_cart.php?user_id={$row['id']}'>View Cart</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>
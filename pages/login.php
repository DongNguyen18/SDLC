<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra thông tin đăng nhập
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Lưu thông tin người dùng vào session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Chuyển hướng dựa trên vai trò
        if ($user['role'] == 'admin') {
            header('Location: ../admin/admin.php');
        } else {
            header('Location: ../index.php');
        }
        exit();
    } else {
        $login_error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        body { 
            display: flex; justify-content: center; align-items: center; min-height: 100vh; 
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1); 
            animation: gradientBG 15s ease infinite; 
            background-size: 200% 200%; /* Đảm bảo gradient chuyển động mượt mà */
        }
        @keyframes gradientBG { 
            0% { background-position: 0% 50%; } 
            50% { background-position: 100% 50%; } 
            100% { background-position: 0% 50%; } 
        }
        .container { 
            background: rgba(255, 255, 255, 0.95); padding: 50px; border-radius: 20px; 
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2); max-width: 450px; text-align: center; 
            position: relative; overflow: hidden; border: 2px solid #ff6b6b; 
        }
        .container::before { 
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; 
            background: linear-gradient(120deg, rgba(255, 107, 107, 0.2), rgba(78, 205, 196, 0.2)); 
            transform: rotate(30deg); animation: shine 6s infinite linear; 
            pointer-events: none; /* Đảm bảo không chặn sự kiện click */
        }
        @keyframes shine { 
            0% { transform: rotate(30deg) translateX(-100%); } 
            100% { transform: rotate(30deg) translateX(100%); } 
        }
        h2 { 
            margin-bottom: 25px; color: #ff6b6b; font-size: 32px; font-weight: 700; 
            text-transform: uppercase; letter-spacing: 2px; text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); 
        }
        .error { 
            color: #ff4757; margin-bottom: 20px; font-size: 16px; 
            background: rgba(255, 71, 87, 0.1); padding: 10px; border-radius: 5px; 
        }
        form { display: flex; flex-direction: column; }
        label { 
            text-align: left; margin-bottom: 8px; color: #333; font-size: 16px; font-weight: 500; 
        }
        input[type="text"], input[type="password"] { 
            width: 100%; padding: 15px; margin-bottom: 25px; border: none; border-radius: 10px; 
            font-size: 16px; background: #f1f2f6; box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05); 
            transition: all 0.3s ease; 
        }
        input[type="text"]:focus, input[type="password"]:focus { 
            background: #fff; box-shadow: 0 0 15px rgba(255, 107, 107, 0.5); transform: scale(1.02); outline: none; 
        }
        button { 
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4); color: #fff; padding: 15px; 
            border: none; border-radius: 10px; font-size: 18px; font-weight: 600; cursor: pointer; 
            transition: all 0.4s ease; box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4); 
        }
        button:hover { 
            background: linear-gradient(90deg, #4ecdc4, #ff6b6b); transform: translateY(-3px); 
            box-shadow: 0 8px 20px rgba(78, 205, 196, 0.5); 
        }
        p { margin-top: 25px; color: #555; font-size: 16px; }
        a { color: #ff6b6b; text-decoration: none; font-weight: 600; transition: all 0.3s ease; }
        a:hover { color: #4ecdc4; text-shadow: 0 0 10px rgba(78, 205, 196, 0.5); }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($login_error)) : ?>
            <p class="error"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
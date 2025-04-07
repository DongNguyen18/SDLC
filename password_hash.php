<?php
// Kết nối đến cơ sở dữ liệu
include 'includes/db.php';

// Thông tin tài khoản cần thêm
$username = 'admin'; // Tên đăng nhập
$email = 'admin@example.com'; // Email
$password = 'admin'; // Mật khẩu gốc (có thể thay đổi)
$role = 'admin'; // Vai trò của người dùng

// Băm mật khẩu
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Kiểm tra xem tài khoản đã tồn tại chưa
$stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

if (!$user) {
    // Thêm tài khoản vào cơ sở dữ liệu
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password,
        'role' => $role
    ]);
    echo "Tài khoản đã được thêm thành công!";
} else {
    echo "Tài khoản đã tồn tại.";
}
?>
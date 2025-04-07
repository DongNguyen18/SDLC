<?php
session_start();
include 'includes/db.php';

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$totalItems = $stmt->fetchColumn() ?: 0;

echo json_encode(['cartCount' => $totalItems]);
?>
<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['cartCount' => 0]);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$totalItems = $stmt->fetchColumn() ?: 0;

echo json_encode(['cartCount' => $totalItems]);
?>

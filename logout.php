<?php
session_start();
session_unset();
session_destroy();

// Kiểm tra nếu login.php nằm trong thư mục "pages/"
if (file_exists('pages/login.php')) {
    header('Location: pages/login.php');
} else {
    header('Location: login.php');
}
exit();
?>

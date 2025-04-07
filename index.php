<?php
session_start();
include 'includes/db.php';

// Lấy danh mục từ URL (nếu có)
$category = isset($_GET['category']) ? $_GET['category'] : null;
// Lấy từ khóa tìm kiếm từ URL (nếu có)
$search = isset($_GET['query']) ? trim($_GET['query']) : null;
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Homepage - Electronics Store</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- Top Header -->
    <header class="top-header">
        <div class="logo">
            <img src="Ảnh/LOGO.jpg" alt="Logo">
        </div>
        <nav class="auth-links">
            <?php if (isset($_SESSION['username'])) : ?>
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                <?php if ($_SESSION['role'] == 'admin') : ?>
                    <a href="admin/admin.php" class="admin-button">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else : ?>
                <a href="pages/register.php">Register</a>
                <a href="pages/login.php">Login</a>
            <?php endif; ?>
            <a href="pages/cart.php" class="cart">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count">0</span>
            </a>
        </nav>
    </header>

    <!-- Main Navigation and Search Bar -->
    <nav class="main-navigation">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#categories">Categories</a></li>
            <li><a href="#new-arrivals">New Arrivals</a></li>
        </ul>
        <form class="search-bar" method="GET" action="index.php">
            <input type="text" name="query" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </nav>

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="slideshow">
            <img src="Ảnh/banner1.png" alt="Banner 1">
            <img src="Ảnh/banner2.jpg" alt="Banner 2">
            <img src="Ảnh/banner3.webp" alt="Banner 3">
        </div>
    </section>

    <!-- Product Categories -->
    <section id="categories" class="product-categories">
        <h2>Categories</h2>
        <div class="categories-grid">
            <div class="category">
                <a href="?category=phone">
                    <h3>Phone</h3>
                </a>
            </div>
            <div class="category">
                <a href="?category=computer">
                    <h3>Computer</h3>
                </a>
            </div>
            <div class="category">
                <a href="?category=gaming">
                    <h3>Game console</h3>
                </a>
            </div>
            <div class="category">
                <a href="?category=home_appliance">
                    <h3>Household appliances</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- Hiển thị sản phẩm theo danh mục hoặc tìm kiếm -->
    <section id="product-section" class="category-products">
        <h2>
            <?php 
            if ($search) {
                echo "Search Results for: " . htmlspecialchars($search);
            } elseif ($category) {
                echo ucfirst($category) . " Products";
            } else {
                echo "All Products";
            }
            ?>
        </h2>
        <div class="products-grid">
            <?php
            if ($search) {
                // Tìm kiếm sản phẩm theo từ khóa
                $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE :search OR description LIKE :search");
                $stmt->execute(['search' => "%$search%"]);
            } elseif ($category) {
                // Lọc sản phẩm theo danh mục
                $stmt = $conn->prepare("SELECT * FROM products WHERE category = :category");
                $stmt->execute(['category' => $category]);
            } else {
                // Hiển thị tất cả sản phẩm
                $stmt = $conn->prepare("SELECT * FROM products");
                $stmt->execute();
            }

            $products = $stmt->fetchAll();

            if (count($products) > 0) {
                foreach ($products as $row) {
                    // Điều chỉnh giá hiển thị
                    $display_price = floatval($row['price']);
                    if ($display_price < 1000 && in_array($row['category'], ['computer', 'gaming', 'home_appliance'])) {
                        $display_price = $display_price * 1000000;
                    }
                    $formattedPrice = number_format($display_price, 0, ',', '.');

                    echo "<div class='product'>
                            <img src='images/{$row['image']}' alt='{$row['product_name']}' width='150'>
                            <h3>{$row['product_name']}</h3>
                            <p>Price: {$formattedPrice} VND</p>
                            <button onclick='addToCart({$row['id']})'>Add to Cart</button>";

                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                        echo "<div class='admin-actions'>
                                <a href='admin/edit_product.php?id={$row['id']}'>Edit</a>
                                <a href='admin/delete_product.php?id={$row['id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </div>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>No products found.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Đánh giá từ người dùng -->
    <section class="user-reviews">
        <h2>Đánh Giá Từ Khách Hàng</h2>
        <div class="review-container">
            <div class="reviews">
                <div class="review-slide">
                    <img src="Ảnh/ng1.jpg" alt="User 1">
                    <div class="name">Trần Minh Hoàng</div>
                    <div class="rating">★★★★★</div>
                    <div class="comment">Gaming laptops here are awesome, powerful performance and reasonable price!</div>
                </div>
                <div class="review-slide">
                    <img src="Ảnh/ng2.jpg" alt="User 2">
                    <div class="name">Phạm Quỳnh Anh</div>
                    <div class="rating">★★★★☆</div>
                    <div class="comment">Genuine phone, but delivery takes a bit long.</div>
                </div>
                <div class="review-slide">
                    <img src="Ảnh/ng3.jpg" alt="User 3">
                    <div class="name">Nguyễn Văn Dũng</div>
                    <div class="rating">★★★★★</div>
                    <div class="comment">Very good customer service, reputable warranty.</div>
                </div>
                <div class="review-slide">
                    <img src="Ảnh/ng4.jpg" alt="User 4">
                    <div class="name">Lê Ngọc Hải</div>
                    <div class="rating">★★★☆☆</div>
                    <div class="comment">The price is a bit high compared to some other places, but the quality is guaranteed.</div>
                </div>
                <div class="review-slide">
                    <img src="Ảnh/ng5.jpg" alt="User 5">
                    <div class="name">Bùi Thị Hồng</div>
                    <div class="rating">★★★★★</div>
                    <div class="comment">Product as described, worth buying!</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="contact-info">
                    <h1>TECHZONE - AUTHENTIC ELECTRONICS STORE</h1>
                    <p>TechZone specializes in providing genuine phones, laptops, gaming gear, and technology accessories.</p>
                    <p><strong>VAT ID:</strong> 1234567890</p>
                    <p><strong>Sales Support:</strong> 0987.654.321</p>
                    <p><strong>Technical Support:</strong> 0978.123.456</p>
                    <p><strong>Address:</strong> 123 Nguyễn Trãi, Thanh Xuân, Hà Nội</p>
                </div>

                <div class="about">
                    <h2>ABOUT TECHZONE</h2>
                    <ul>
                        <li>Introduction</li>
                        <li>Technology News</li>
                        <li>Careers</li>
                    </ul>
                </div>

                <div class="policy">
                    <h2>PURCHASE POLICY</h2>
                    <ul>
                        <li>Manufacturer's Warranty</li>
                        <li>Return Policy</li>
                        <li>Nationwide Delivery</li>
                        <li>Terms & Payment</li>
                        <li>Shopping Guide</li>
                    </ul>
                </div>

                <div class="social-links">
                    <h2>CONNECT WITH TECHZONE</h2>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <p class="copyright">© 2024 TechZone - All rights reserved.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script>
        // Cuộn xuống phần sản phẩm khi tìm kiếm thành công
        window.onload = function() {
            <?php if ($search) : ?>
                document.getElementById('product-section').scrollIntoView({ behavior: 'smooth' });
            <?php endif; ?>
        };
    </script>
</body>
</html>
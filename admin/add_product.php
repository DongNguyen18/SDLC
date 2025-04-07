<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

include '../includes/db.php';

// Xử lý thông báo
$success_message = '';
$error_message = '';

// Xử lý thêm và cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $product_code = $_POST['product_code'];
    $product_name = $_POST['product_name'];
    $price = floatval($_POST['price']); // Chuyển đổi giá thành số thực
    $category = $_POST['category'];
    $quantity = intval($_POST['quantity']); // Chuyển đổi số lượng thành số nguyên
    $description = $_POST['description'];
    $image_name = '';

    // Kiểm tra giá trị giá khi nhập
    if ($price < 1000) { // Giả định giá tối thiểu phải lớn hơn 1000 (1,000 VND)
        $price = $price * 1000000; // Nhân với 1,000,000 nếu giá nhỏ hơn 1000
    }

    // Xử lý ảnh nếu có upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../images/";
        $image_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra file ảnh
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false || $_FILES['image']['size'] > 5000000 || !in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error_message = "Invalid image file!";
        } else {
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        }
    }

    if (empty($error_message)) {
        // Nếu có ID, thực hiện UPDATE, nếu không INSERT sản phẩm mới
        if ($product_id) {
            $stmt = $conn->prepare("UPDATE products SET product_code = :product_code, product_name = :product_name, price = :price, category = :category, quantity = :quantity, description = :description" . ($image_name ? ", image = :image" : "") . " WHERE id = :id");
            $params = [
                'product_code' => $product_code,
                'product_name' => $product_name,
                'price' => $price,
                'category' => $category,
                'quantity' => $quantity,
                'description' => $description,
                'id' => $product_id
            ];
            if ($image_name) $params['image'] = $image_name;
            $stmt->execute($params);
            $success_message = "Product updated successfully!";
        } else {
            $stmt = $conn->prepare("INSERT INTO products (product_code, product_name, price, category, quantity, image, description) VALUES (:product_code, :product_name, :price, :category, :quantity, :image, :description)");
            $stmt->execute([
                'product_code' => $product_code,
                'product_name' => $product_name,
                'price' => $price,
                'category' => $category,
                'quantity' => $quantity,
                'image' => $image_name,
                'description' => $description
            ]);
            $success_message = "Product added successfully!";
        }
    }
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $delete_id]);
    $success_message = "Product deleted successfully!";
}

// Lấy danh sách sản phẩm
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
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

        .message {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .success {
            background: rgba(46, 213, 115, 0.1);
            color: #2ed573;
        }

        .error {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
        }

        form {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
            overflow: hidden;
        }

        form::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(120deg, rgba(255, 107, 107, 0.2), rgba(78, 205, 196, 0.2));
            transform: rotate(30deg);
            animation: shine 6s infinite linear;
            pointer-events: none;
        }

        @keyframes shine {
            0% { transform: rotate(30deg) translateX(-100%); }
            100% { transform: rotate(30deg) translateX(100%); }
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-size: 16px;
            font-weight: 500;
        }

        input[type="text"], input[type="number"], select, textarea, input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            background: #f1f2f6;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, input[type="number"]:focus, select:focus, textarea:focus, input[type="file"]:focus {
            background: #fff;
            box-shadow: 0 0 15px rgba(255, 107, 107, 0.5);
            transform: scale(1.02);
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button {
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
            color: #fff;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.4s ease;
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }

        button:hover {
            background: linear-gradient(90deg, #4ecdc4, #ff6b6b);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(78, 205, 196, 0.5);
        }

        h2 {
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
            color: #ffd700;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
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
        }

        tr:nth-child(even) {
            background: rgba(241, 242, 246, 0.5);
        }

        tr:hover {
            background: rgba(78, 205, 196, 0.2);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        img {
            border-radius: 5px;
            object-fit: cover;
        }

        td button {
            background: #ff6b6b;
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        td button:hover {
            background: #4ecdc4;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(78, 205, 196, 0.4);
        }

        td a {
            color: #ff4757;
            text-decoration: none;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.3s ease;
        }

        td a:hover {
            color: #2ed573;
            text-shadow: 0 0 5px rgba(46, 213, 115, 0.5);
        }
    </style>
</head>
<body>
    <h1>Manage Products</h1>
    <a href="admin.php">Back to Admin Dashboard</a>

    <?php if (!empty($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" id="product_id" name="product_id">

        <label for="product_code">Product Code:</label>
        <input type="text" id="product_code" name="product_code" required>

        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required>

        <label for="price">Price (VND):</label>
        <input type="number" id="price" name="price" step="0.01" min="0" required>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="phone">Phone</option>
            <option value="computer">Computer</option>
            <option value="gaming">Game console</option>
            <option value="home_appliance">Household appliances</option>
        </select>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" min="0" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="image">Product Image:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">Save Product</button>
    </form>

    <h2>Product List</h2>
    <table>
        <tr>
            <th>Image</th>
            <th>Code</th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td>
                <?php if (!empty($product['image']) && file_exists("../images/" . $product['image'])): ?>
                    <img src="../images/<?php echo htmlspecialchars($product['image']); ?>" width="50">
                <?php else: ?>
                    <span>No Image</span>
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($product['product_code']); ?></td>
            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
            <td>
                <?php 
                // Kiểm tra và điều chỉnh giá hiển thị
                $display_price = floatval($product['price']);
                if ($display_price < 1000 && in_array($product['category'], ['computer', 'gaming', 'home_appliance'])) {
                    $display_price = $display_price * 1000000;
                }
                echo number_format($display_price, 0, ',', '.') . ' VND'; 
                ?>
            </td>
            <td><?php echo htmlspecialchars($product['category']); ?></td>
            <td><?php echo htmlspecialchars($product['quantity']); ?></td>
            <td>
                <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">Edit</button>
                <a href="?delete=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script>
    function editProduct(product) {
        document.getElementById('product_id').value = product.id;
        document.getElementById('product_code').value = product.product_code;
        document.getElementById('product_name').value = product.product_name;
        document.getElementById('price').value = product.price;
        document.getElementById('category').value = product.category;
        document.getElementById('quantity').value = product.quantity;
        document.getElementById('description').value = product.description;
    }
    </script>
</body>
</html>
function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ productId: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById("cart-count").textContent = data.cartCount; // Cập nhật số lượng giỏ hàng
        } else {
            alert('Failed to add product to cart: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => console.error('Error:', error));
}

// Khi tải trang, lấy số lượng sản phẩm từ session và cập nhật giỏ hàng
document.addEventListener("DOMContentLoaded", function() {
    fetch('get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById("cart-count").textContent = data.cartCount;
    })
    .catch(error => console.error('Error:', error));
});

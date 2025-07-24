<?php
session_start();
include('Mysqlconnection.php');

// Fetch product from the database
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM Products WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<p>Product not found.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | XD Hobby Shop</title>
    <link rel="stylesheet" href="main.css">
    <script>
        // Function to update the cart count
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const totalItems = cart.reduce((acc, item) => acc + item.quantity, 0);
            document.getElementById('cartCount').innerText = totalItems > 0 ? `(${totalItems})` : '';
        }

        // Function to open the cart modal
        function openCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartContainer = document.getElementById('cartItems');
            cartContainer.innerHTML = '';  // Clear the cart content before adding new items

            if (cart.length > 0) {
                cart.forEach((item, index) => {
                    const productItem = document.createElement('div');
                    productItem.classList.add('cart-item');
                    productItem.innerHTML = `
                        <p><strong>${item.name}</strong></p>
                        <p>₱${item.price.toFixed(2)} x ${item.quantity}</p>
                        <p>Total: ₱${(item.price * item.quantity).toFixed(2)}</p>
                        <button onclick="removeFromCart(${index})">Remove</button>
                        <button onclick="decreaseQuantity(${index})">Decrease Quantity</button>
                    `;
                    cartContainer.appendChild(productItem);
                });
            } else {
                cartContainer.innerHTML = '<p>Your cart is empty.</p>';
            }

            // Add Checkout Button
            const checkoutButton = document.createElement('button');
            checkoutButton.innerText = 'Checkout';
            checkoutButton.onclick = function() {
                // Redirect to checkout_page.php
                window.location.href = 'checkout_page.php';
            };
            cartContainer.appendChild(checkoutButton);

            // Toggle the cart modal visibility
            document.getElementById('cartModal').style.display = 'block';
        }

        // Remove item from cart
        function removeFromCart(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);  // Remove the item from the cart at the specified index

            // Save the updated cart back to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            updateCartCount();
            openCart();  // Reopen the cart to reflect the changes
        }

        // Decrease the quantity of the product in the cart
        function decreaseQuantity(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            if (cart[index].quantity > 1) {
                cart[index].quantity -= 1; // Decrease the quantity
            } else {
                // If quantity is 1, remove the item from the cart
                cart.splice(index, 1);
            }

            // Save the updated cart back to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            updateCartCount();
            openCart();  // Reopen the cart to reflect the changes
        }

        // Close the cart modal
        function closeCart() {
            document.getElementById('cartModal').style.display = 'none';
        }

        // Add to cart function
        function addToCart(productId, productName, productPrice) {
            const quantity = parseInt(document.getElementById('quantity').value);

            if (quantity < 1) {
                alert("Quantity must be at least 1.");
                return;
            }

            // Get cart from localStorage, or create a new one if it doesn't exist
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            // Check if the product is already in the cart
            const productIndex = cart.findIndex(item => item.product_id === productId);

            if (productIndex >= 0) {
                // If the product is already in the cart, just increase the quantity
                cart[productIndex].quantity += quantity;
            } else {
                // If the product is not in the cart, add it with the specified quantity
                cart.push({
                    product_id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: quantity
                });
            }

            // Save the updated cart back to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            // Show confirmation message
            document.getElementById('cartMessage').innerText = 'Added to Cart';
            setTimeout(() => {
                document.getElementById('cartMessage').innerText = '';
            }, 2000);  // Hide message after 2 seconds

            updateCartCount();
        }

        // Initialize cart count on page load
        window.onload = updateCartCount;
    </script>
</head>
<body>
    <header>
        <h1>XD Hobby Shop</h1>
        <nav>
            <a href="index.php">Home</a>
            <?php if (!isset($_SESSION['username'])): ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php else: ?>
                <a href="logout.php">Logout</a>
                <a href="user_account.php" class="account-button">Account</a>
            <?php endif; ?>
            <button id="checkCartBtn" onclick="openCart()" style="border: 1px solid; padding: 5px 10px;">
                Check Cart <span id="cartCount"></span>
            </button>
        </nav>
    </header>

    <section class="content">
        <div class="product-detail">
            <?php if (!empty($product['image_url'])): ?>
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width:350px; border-radius:12px;">
            <?php endif; ?>
            <div>
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="price">₱<?php echo number_format($product['price'], 2); ?> PHP</p>
                <?php if (isset($_SESSION['username'])): ?>
                    <form method="POST" action="buy_product.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <label for="quantity">Quantity</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" required>
                        <button type="button" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">Add to Cart</button>
                        <button type="button" onclick="window.history.back();">Back</button>
                    </form>
                    <p id="cartMessage" style="color: green;"></p>
                <?php else: ?>
                    <p><a href="login.php">Login</a> to buy this product.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Cart Modal -->
    <div id="cartModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.5); padding:20px;">
        <div style="background-color:white; padding:20px; border-radius:5px; max-width:600px; margin:auto;">
            <h3>Your Cart</h3>
            <div id="cartItems"></div>
            <button onclick="closeCart()">Close</button>
        </div>
    </div>
</body>
</html>

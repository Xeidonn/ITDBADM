<?php
session_start();
include('Mysqlconnection.php');

// Fetch products from the database
$sql = "SELECT * FROM Products";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | XD Hobby Shop</title>
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
                        <button onclick="removeFromCart(${index})">Remove all</button>
                        <button onclick="decreaseQuantity(${index})">Remove</button>
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

        // Initialize cart count on page load
        window.onload = updateCartCount;
    </script>
    <style>
        .product-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f0f0f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-detail img {
            max-width: 350px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .product-detail div {
            flex: 1;
            margin-left: 20px;
        }

        .product-detail h2 {
            font-size: 2rem;
            color: #3E5F44;
        }

        .product-detail p {
            color: #5E936C;
            font-size: 1.1rem;
            margin: 10px 0;
        }

        .product-detail .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #3E5F44;
        }

        .product-detail .buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .product-detail .buttons button {
            background-color: #3E5F44;
            color: white;
            padding: 10px 20px;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .product-detail .buttons button:hover {
            background-color: #5E936C;
        }

        /* Cart Modal */
        .cart-item {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        .cart-item button {
            background-color: #d9534f;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }

        .cart-item button:hover {
            background-color: #c9302c;
        }

        /* Account Button */
        .account-button {
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .account-button:hover {
            background-color: #3E5F44;
        }

        /* Hide Add to Cart Button if Logged Out */
        .add-to-cart-btn {
            display: block;
        }

        .add-to-cart-btn.logged-out {
            display: none;
        }
    </style>
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
            <!-- Conditionally hide "Add to Cart" button when logged out -->
            <?php if(isset($_SESSION['username'])): ?>
                <button id="checkCartBtn" onclick="openCart()" style="border: 1px solid; padding: 5px 10px;">
                    Check Cart <span id="cartCount"></span>
                </button>
            <?php endif; ?>
        </nav>
    </header>

    <section class="hero">
        <h2>Welcome to XD Hobby Shop!<br>Explore the best Trading Card Games</h2>
    </section>

    <section class="content">
        <h3>Featured Product!</h3>
        <div class="products">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="card">
                    <a href="product.php?id=<?php echo $product['product_id']; ?>">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width:350px; border-radius:12px;">
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="price">₱<?php echo number_format($product['price'], 2); ?> PHP</p>
                    </a>
                    <?php if (isset($_SESSION['username'])): ?>
                        <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">Add to Cart</button>
                    <?php else: ?>
                        <button class="add-to-cart-btn logged-out" disabled>Add to Cart (Login Required)</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
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

    <script>
        function addToCart(productId, productName, productPrice) {
            // Get cart from localStorage, or create a new one if it doesn't exist
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            // Check if the product is already in the cart
            const productIndex = cart.findIndex(item => item.product_id === productId);

            if (productIndex >= 0) {
                // If the product is already in the cart, just increase the quantity
                cart[productIndex].quantity += 1;
            } else {
                // If the product is not in the cart, add it
                cart.push({
                    product_id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1
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
    </script>
</body>
</html>

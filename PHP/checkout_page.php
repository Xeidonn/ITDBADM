<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | XD Hobby Shop</title>
    <link rel="stylesheet" href="main.css">
    <script>
        // Function to load cart items from localStorage and display them on the page
        function loadCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartContainer = document.getElementById('cartDetails');
            let totalAmount = 0;

            if (cart.length > 0) {
                cart.forEach(item => {
                    const productItem = document.createElement('div');
                    productItem.classList.add('checkout-item');
                    productItem.innerHTML = `
                        <p><strong>${item.name}</strong></p>
                        <p>₱${item.price.toFixed(2)} x ${item.quantity}</p>
                        <p>Total: ₱${(item.price * item.quantity).toFixed(2)}</p>
                    `;
                    cartContainer.appendChild(productItem);
                    totalAmount += item.price * item.quantity;
                });

                // Display total amount
                const totalElement = document.createElement('p');
                totalElement.innerHTML = `<strong>Total: ₱${totalAmount.toFixed(2)}</strong>`;
                cartContainer.appendChild(totalElement);
            } else {
                cartContainer.innerHTML = '<p>Your cart is empty.</p>';
            }

            // Store the total amount in localStorage for the payment page
            localStorage.setItem('totalAmount', totalAmount.toFixed(2));
        }

        // Initialize cart details on page load
        window.onload = loadCart;
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
            <?php endif; ?>
        </nav>
    </header>

    <section class="content">
        <h3>Checkout</h3>
        <div id="cartDetails" class="checkout-details"></div>
        <!-- Updated button to go back to home page (index.php) -->
        <button onclick="window.location.href='index.php';">Back to Shopping</button>
        <br>
        <a href="payment_page.php">
            <button>Proceed to Payment</button>
        </a>
    </section>

    <style>
        .checkout-item {
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }

        .checkout-details {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .checkout-details p {
            font-size: 1.2rem;
            color: #3E5F44;
        }

        button {
            background-color: #3E5F44;
            color: white;
            padding: 10px 20px;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #5E936C;
        }
    </style>
</body>
</html>

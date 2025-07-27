<?php
session_start();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | XD Hobby Shop</title>
    <link rel="stylesheet" href="../CSS/main.css">
    <script>
        // Function to load cart items from localStorage and display them on the page
        function loadCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartContainer = document.getElementById('cartDetails');
            let totalAmount = 0;

            cartContainer.innerHTML = ''; // Clear previous content

            if (cart.length > 0) {
                cart.forEach(item => {
                    const productItem = document.createElement('div');
                    productItem.classList.add('checkout-item');
                    productItem.innerHTML = `
                        <p><strong>${item.name}</strong></p>
                        <p>${item.symbol}${item.price.toFixed(2)} ${item.currency_code} x ${item.quantity}</p>
                        <p>Total: ${item.symbol}${(item.price * item.quantity).toFixed(2)} ${item.currency_code}</p>
                    `;
                    cartContainer.appendChild(productItem);
                    totalAmount += item.price * item.quantity;
                });

                // Display total amount (use the symbol and code of the first item)
                const totalElement = document.createElement('p');
                const symbol = cart[0].symbol || '';
                const currency_code = cart[0].currency_code || '';
                totalElement.innerHTML = `<strong>Total: ${symbol}${totalAmount.toFixed(2)} ${currency_code}</strong>`;
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
        <button class="checkout-btn-back" onclick="window.location.href='index.php';">Back to Shopping</button>
        <br>
        <a href="payment_page.php">
            <button class="checkout-btn" type="button">Proceed to Payment</button>
        </a>
    </section>
</body>
</html>
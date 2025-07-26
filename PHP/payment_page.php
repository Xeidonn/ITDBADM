<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment | XD Hobby Shop</title>
    <link rel="stylesheet" href="../CSS/main.css">
    <script>
        // Function to calculate the converted price based on selected currency
        function convertCurrency() {
            const currency = document.getElementById('currency').value;
            const totalAmount = parseFloat(localStorage.getItem('totalAmount'));

            let convertedAmount;
            let currencySymbol;
            let conversionRate = 1;

            // Define conversion rates (for simplicity, using static rates)
            if (currency === 'PHP') {
                currencySymbol = '₱';
            }
            else if (currency === 'JPY') {
                conversionRate = 2.5; // 1 PHP = 2.5 JPY (example rate)
                currencySymbol = '¥';
            } else if (currency === 'USD') {
                conversionRate = 0.02; // 1 PHP = 0.02 USD (example rate)
                currencySymbol = '$';
            } else if (currency === 'EUR') {
                conversionRate = 0.018; // 1 PHP = 0.018 EUR (example rate)
                currencySymbol = '€';
            }

            // Calculate the converted amount
            convertedAmount = totalAmount * conversionRate;

            // Display the converted amount
            document.getElementById('convertedAmount').innerText = currencySymbol + convertedAmount.toFixed(2);
        }

        // Function to show the appropriate payment form based on the selected method
        function showPaymentForm() {
            const paymentMethod = document.getElementById('paymentMethod').value;

            // Hide all payment forms first
            document.getElementById('creditCardForm').style.display = 'none';
            document.getElementById('debitCardForm').style.display = 'none';
            document.getElementById('codMessage').style.display = 'none';

            if (paymentMethod === 'creditCard') {
                document.getElementById('creditCardForm').style.display = 'block';
            } else if (paymentMethod === 'debitCard') {
                document.getElementById('debitCardForm').style.display = 'block';
            } else if (paymentMethod === 'cashOnDelivery') {
                document.getElementById('codMessage').style.display = 'block';
            }
        }

        // Initialize the page and load the total amount
        window.onload = function() {
            const totalAmount = localStorage.getItem('totalAmount');
            document.getElementById('totalAmount').innerText = '₱' + totalAmount;

            // Convert the amount to the selected currency on page load
            convertCurrency();
        }
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
        <h3>Proceed to Payment</h3>
        <div>
            <p><strong>Total Amount (PHP): </strong><span id="totalAmount"></span></p>

            <!-- Payment Method Selection -->
            <p><strong>Payment Method:</strong></p>
            <select id="paymentMethod" onchange="showPaymentForm()">
                <option value="creditCard">Credit Card</option>
                <option value="debitCard">Debit Card</option>
                <option value="cashOnDelivery">Cash on Delivery</option>
            </select>

            <br><br>

            <!-- Currency Selection -->
            <p><strong>Select Currency:</strong></p>
            <select id="currency" onchange="convertCurrency()">
                <option value="PHP">PHP</option>
                <option value="USD">USD</option>
                <option value="JPY">JPY</option>
                <option value="EUR">EUR</option>
            </select>

            <br><br>

            <!-- Converted Price -->
            <p><strong>Converted Amount:</strong> <span id="convertedAmount"></span></p>

            <!-- Credit Card Details -->
            <div id="creditCardForm" style="display:none;">
                <p><strong>Enter Credit Card Details:</strong></p>
                <input type="text" placeholder="Card Number" id="cardNumber"><br><br>
                <input type="text" placeholder="Expiration Date" id="expDate"><br><br>
                <input type="text" placeholder="CVV" id="cvv"><br><br>
            </div>

            <!-- Debit Card Details -->
            <div id="debitCardForm" style="display:none;">
                <p><strong>Enter Debit Card Details:</strong></p>
                <input type="text" placeholder="Card Number" id="debitCardNumber"><br><br>
                <input type="text" placeholder="Expiration Date" id="debitExpDate"><br><br>
                <input type="text" placeholder="CVV" id="debitCvv"><br><br>
            </div>

            <!-- Cash on Delivery (COD) Message -->
            <div id="codMessage" style="display:none;">
                <p><strong>You selected Cash on Delivery. Please have the exact amount ready upon delivery.</strong></p>
            </div>

            <!-- Submit Button -->
            <button>Submit Payment</button>
        </div>

        <!-- Back to Checkout -->
        <br>
        <a href="checkout_page.php">
            <button>Back to Checkout</button>
        </a>
    </section>

    <style>
        .content {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

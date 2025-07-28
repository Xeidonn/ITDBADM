<?php
session_start();
include('Mysqlconnection.php');

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $user_id = $_SESSION['user_id'];
    $currency = strtoupper($_POST['currency']); // Normalize input to uppercase
    $total_amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $payment_status = 'Paid'; // or 'Pending'
    $cart = json_decode($_POST['cart_json'], true);

    // Get currency_id from Currencies table
    $currency_id = null;
    $stmt = $conn->prepare("SELECT currency_id FROM Currencies WHERE currency_code = ?");
    $stmt->bind_param("s", $currency);
    $stmt->execute();
    $stmt->bind_result($currency_id);
    $stmt->fetch();
    $stmt->close();

    // Fallback to PHP (ID 1) if not found
    if (!$currency_id) {
        $currency_id = 1;
    }

    // 1. Insert into Orders
    $stmt = $conn->prepare("INSERT INTO Orders (user_id, total_amount, currency_id) VALUES (?, ?, ?)");
    $stmt->bind_param("idi", $user_id, $total_amount, $currency_id);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // 2. Insert each cart item into Order_Items
    foreach ($cart as $item) {
        $stmt = $conn->prepare("INSERT INTO Order_Items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // 3. Insert into Transaction_Log
    $stmt = $conn->prepare("INSERT INTO Transaction_Log (order_id, payment_method, payment_status, amount, currency_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdi", $order_id, $payment_method, $payment_status, $total_amount, $currency_id);
    $stmt->execute();
    $stmt->close();

    // Clear cart and redirect
    echo "<script>localStorage.removeItem('cart');localStorage.removeItem('totalAmount');</script>";
    echo "<script>alert('Order placed successfully!');window.location.href='index.php';</script>";
    exit;
}
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
                conversionRate = 2.58; // 1 PHP = 2.58 JPY (example rate)
                currencySymbol = '¥';
            } else if (currency === 'USD') {
                conversionRate = 0.017; // 1 PHP = 0.017 USD (example rate)
                currencySymbol = '$';
            } else if (currency === 'EUR') {
                conversionRate = 0.015; // 1 PHP = 0.015 EUR (example rate)
                currencySymbol = '€';
            }  else if (currency === 'KRW') {
                conversionRate = 24; // 1 PHP = 0.015 EUR (example rate)
                currencySymbol = '₩';
            }

            // Calculate the converted amount
            convertedAmount = totalAmount * conversionRate;

            // Display the converted amount
            document.getElementById('convertedAmount').innerText = currencySymbol + convertedAmount.toFixed(2);

            // Set hidden amount and currency for form submission
            document.getElementById('hiddenAmount').value = convertedAmount.toFixed(2);
            document.getElementById('currency_hidden').value = currency;
        }

        // Function to show the appropriate payment form based on the selected method
        function showPaymentForm() {
            const paymentMethod = document.getElementById('paymentMethod').value;

            // Hide all payment forms first
            document.getElementById('creditCardForm').style.display = 'none';
            document.getElementById('debitCardForm').style.display = 'none';

            if (paymentMethod === 'creditCard') {
                document.getElementById('creditCardForm').style.display = 'block';
            } else if (paymentMethod === 'debitCard') {
                document.getElementById('debitCardForm').style.display = 'block';
            }
        }

        // Before submitting, set hidden fields for amount, currency, and cart
        function preparePaymentForm() {
            // Set amount and currency
            convertCurrency();

            // Set cart JSON
            const cart = localStorage.getItem('cart');
            document.getElementById('cart_json').value = cart ? cart : '[]';
            return true;
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
        <form method="POST" action="payment_page.php" onsubmit="return preparePaymentForm();">
            <p><strong>Total Amount (PHP): </strong><span id="totalAmount"></span></p>

            <!-- Payment Method Selection -->
            <p><strong>Payment Method:</strong></p>
            <select id="paymentMethod" name="payment_method" onchange="showPaymentForm()" required>
                <option value="creditCard">Credit Card</option>
                <option value="debitCard">Debit Card</option>
            </select>

            <br><br>

            <!-- Currency Selection -->
            <p><strong>Select Currency:</strong></p>
            <select id="currency" name="currency" onchange="convertCurrency()" required>
                <option value="PHP">PHP</option>
                <option value="USD">USD</option>
                <option value="JPY">JPY</option>
                <option value="EUR">EUR</option>
                <option value="KRW">KRW</option>
            </select>

            <br><br>

            <!-- Converted Price -->
            <p><strong>Converted Amount:</strong> <span id="convertedAmount"></span></p>

            <!-- Credit Card Details (Removed) -->

            <!-- Debit Card Details (Removed) -->

            <!-- Hidden fields for backend processing -->
            <input type="hidden" name="amount" id="hiddenAmount" value="">
            <input type="hidden" name="currency" id="currency_hidden" value="">
            <input type="hidden" name="cart_json" id="cart_json" value="">

            <!-- Submit Button -->
            <button type="submit" name="submit_payment">Submit Payment</button>
        </form>

        <!-- Back to Checkout -->
        <br>
        <a href="checkout_page.php">
            <button type="button">Back to Checkout</button>
        </a>
    </section>
</body>
</html>

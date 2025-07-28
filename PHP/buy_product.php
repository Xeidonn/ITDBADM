<?php
    session_start();
    include('Mysqlconnection.php');

    // Check if the user is logged in and is a customer
    if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
        header("Location: login.php");
        exit();
    }

    // Fetch product details
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $sql = "SELECT * FROM Products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product) {
        // Calculate total price
        $total_amount = $product['price'] * $quantity;

        // Insert order into Orders table
        $sql_order = "INSERT INTO Orders (user_id, total_amount, currency_id) VALUES (?, ?, ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bind_param("idi", $_SESSION['user_id'], $total_amount, $product['currency_id']);
        $stmt_order->execute();
        $order_id = $stmt_order->insert_id;  // Get the inserted order ID

        // Insert order items into Order_Items table
        $sql_order_items = "INSERT INTO Order_Items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_order_items = $conn->prepare($sql_order_items);
        $stmt_order_items->bind_param("iiid", $order_id, $product_id, $quantity, $product['price']);
        $stmt_order_items->execute();

        // Update product stock
        $new_stock = $product['stock_quantity'] - $quantity;
        $sql_update_stock = "UPDATE Products SET stock_quantity = ? WHERE product_id = ?";
        $stmt_update_stock = $conn->prepare($sql_update_stock);
        $stmt_update_stock->bind_param("ii", $new_stock, $product_id);
        $stmt_update_stock->execute();

        echo "Purchase successful! Your order ID is " . $order_id;
    } else {
        echo "Product not found!";
    }

    $stmt->close();
    $conn->close();
?>
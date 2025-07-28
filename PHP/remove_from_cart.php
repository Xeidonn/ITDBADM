<?php
session_start();
include('Mysqlconnection.php');

if (isset($_GET['cart_item_id']) && isset($_SESSION['user_id'])) {
    $cart_item_id = $_GET['cart_item_id'];
    $user_id = $_SESSION['user_id'];

    // Remove the item from the cart
    $sql = "DELETE FROM cart_items WHERE cart_item_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_item_id, $user_id);
    $stmt->execute();

    // Close connection and redirect
    $stmt->close();
    header("Location: index.php"); // Redirect back to home page
}
?>
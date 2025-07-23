<?php
    session_start();
    include('Mysqlconnection.php');

    // Check if the user is logged in and is an admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
        header("Location: login.php");  // Redirect to login if not an admin
        exit();
    }

    // Check if product ID is provided
    if (!isset($_GET['id'])) {
        echo "Product not found!";
        exit();
    }

    $product_id = $_GET['id'];

    // Updated SQL query to join Products with Categories and Currencies
    $sql = "SELECT p.*, c.category_name, cur.currency_code, cur.symbol 
            FROM Products p 
            JOIN Categories c ON p.category_id = c.category_id
            JOIN Currencies cur ON p.currency_id = cur.currency_id 
            WHERE p.product_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo "Product not found!";
        exit();
    }

    // Handle the deletion process
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
        // Delete the product from the Products table
        $delete_sql = "DELETE FROM Products WHERE product_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $product_id);

        if ($delete_stmt->execute()) {
            echo "Product deleted successfully!";
            // Redirect to the admin dashboard or product list
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error: " . $delete_stmt->error;
        }

        $delete_stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h2>Delete Product</h2>
    <p><a href="admin_dashboard.php">Back to Dashboard</a></p>

    <h3>Are you sure you want to delete the following product?</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock Quantity</th>
                <th>Category</th>
                <th>Currency</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['description']); ?></td>
                <td><?php echo "₱" . number_format($product['price'], 2); ?></td>
                <td><?php echo $product['stock_quantity']; ?></td>
                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                <td><?php echo htmlspecialchars($product['currency_code']) . " (" . $product['symbol'] . ")"; ?></td>
            </tr>
        </tbody>
    </table>

    <form method="POST" action="delete_product.php?id=<?php echo $product['product_id']; ?>">
        <button type="submit" name="confirm_delete">Yes, Delete Product</button>
    </form>
    
    <p><a href="admin_dashboard.php">Cancel</a></p>
</body>
</html>

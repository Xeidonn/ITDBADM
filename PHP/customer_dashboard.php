<?php
    session_start();
    include('Mysqlconnection.php');

    // Fetch the products from the database
    $sql = "SELECT * FROM Products";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <p><a href="logout.php">Logout</a></p>
    <h3>Products</h3>

    <?php while ($product = $result->fetch_assoc()): ?>
        <div>
            <h4><?php echo $product['name']; ?></h4>
            <p><?php echo $product['description']; ?></p>
            <p>Price: <?php echo $product['price']; ?></p>
            <form method="POST" action="buy_product.php">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <input type="number" name="quantity" value="1" min="1" required>
                <button type="submit">Buy</button>
            </form>
        </div>
    <?php endwhile; ?>

</body>
</html>

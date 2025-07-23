<?php
session_start();
include('Mysqlconnection.php');

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
                        <button type="submit">Add to Cart</button>
                        <button type="button" onclick="window.history.back();">Back</button>
                    </form>
                <?php else: ?>
                    <p><a href="login.php">Login</a> to buy this product.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>
</html>
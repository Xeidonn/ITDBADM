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

    <section class="hero">
        <h2>Welcome to XD Hobby Shop!<br>Explore the best Trading Card Games</h2>
    </section>

    <section class="content">
        <h3>Featured Product!</h3>
        <div class="products">
            <?php while ($product = $result->fetch_assoc()): ?>
                <a class="card" href="product.php?id=<?php echo $product['product_id']; ?>">
                    <?php if (!empty($product['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x220?text=No+Image" alt="No Image">
                    <?php endif; ?>
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p class="price">
                        <?php if (!empty($product['price'])): ?>
                            From ₱<?php echo number_format($product['price'], 2); ?> PHP
                        <?php endif; ?>
                    </p>
                </a>
            <?php endwhile; ?>
        </div>
    </section>
</body>
</html>
<?php
    session_start();
    include('Mysqlconnection.php');

    // Check if the user is logged in and is a staff
    if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
        header("Location: login.php");  // Redirect to login if not a staff
        exit();
    }

    // Check if product ID is provided
    if (!isset($_GET['id'])) {
        echo "Product not found!";
        exit();
    }

    // Fetch the product data from the database
    $product_id = $_GET['id'];
    $sql = "SELECT * FROM Products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo "Product not found!";
        exit();
    }

    // Fetch categories and currencies for the form
    $categories = $conn->query("SELECT * FROM Categories");
    $currencies = $conn->query("SELECT * FROM Currencies");

    $successMessage = "";  // Variable to store success message

    // Handle form submission for updating the product
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];
        $category_id = $_POST['category_id'];
        $currency_id = $_POST['currency_id'];

        // Update the product in the Products table
        $update_sql = "UPDATE Products SET name = ?, description = ?, price = ?, stock_quantity = ?, category_id = ?, currency_id = ? WHERE product_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssdiisi", $name, $description, $price, $stock_quantity, $category_id, $currency_id, $product_id);

        if ($update_stmt->execute()) {
            $successMessage = "Product updated successfully!";
        } else {
            echo "Error: " . $update_stmt->error;
        }

        $update_stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="main.css">
    <script>
        // Show a success notification and redirect after a few seconds
        <?php if ($successMessage): ?>
            window.onload = function() {
                alert("<?php echo $successMessage; ?>");  // Display the success message
                setTimeout(function() {
                    window.location.href = "staff_dashboard.php";  // Redirect to admin dashboard after 3-5 seconds
                }, 3000);  // 3000ms = 3 seconds
            };
        <?php endif; ?>
    </script>
</head>
<body>
    <h2>Edit Product</h2>
    <p><a href="staff_dashboard.php">Back to Dashboard</a></p>

    <h3>Update Product Details</h3>
    <form method="POST" action="edit_product_staff.php?id=<?php echo $product['product_id']; ?>">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label for="price">Price</label>
        <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <label for="stock_quantity">Stock Quantity</label>
        <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" min="0" step="1" required>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <?php while ($category = $categories->fetch_assoc()): ?>
                <option value="<?php echo $category['category_id']; ?>" <?php if ($category['category_id'] == $product['category_id']) echo 'selected'; ?>>
                    <?php echo $category['category_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="currency_id">Currency</label>
        <select id="currency_id" name="currency_id" required>
            <?php while ($currency = $currencies->fetch_assoc()): ?>
                <option value="<?php echo $currency['currency_id']; ?>" <?php if ($currency['currency_id'] == $product['currency_id']) echo 'selected'; ?>>
                    <?php echo $currency['currency_code']; ?> (<?php echo $currency['symbol']; ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="update_product">Update Product</button>
    </form>
</body>
</html>
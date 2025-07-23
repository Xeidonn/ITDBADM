<?php
    session_start();
    include('Mysqlconnection.php');

    // Check if the user is logged in and is an admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
        header("Location: login.php");  // Redirect to login if not an admin
        exit();
    }

    // Handle product addition
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];
        $category_id = $_POST['category_id'];
        $currency_id = $_POST['currency_id'];

        // Insert new product into Products table
        $sql = "INSERT INTO Products (name, description, price, stock_quantity, category_id, currency_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiis", $name, $description, $price, $stock_quantity, $category_id, $currency_id);

        if ($stmt->execute()) {
            echo "Product added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    // Fetch categories and currencies for the form
    $categories = $conn->query("SELECT * FROM Categories");
    $currencies = $conn->query("SELECT * FROM Currencies");

    // Fetch all products for the table
    $products = $conn->query("SELECT p.*, c.category_name, cur.currency_code, cur.symbol FROM Products p 
                              JOIN Categories c ON p.category_id = c.category_id
                              JOIN Currencies cur ON p.currency_id = cur.currency_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h2>Welcome, Admin!</h2>
    <p><a href="logout.php">Logout</a></p>

    <h3>Add Product</h3>
    <form method="POST" action="admin_dashboard.php">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>

        <label for="price">Price</label>
        <input type="text" id="price" name="price" required>
        
        <label for="stock_quantity">Stock Quantity</label>
        <input type="number" id="stock_quantity" name="stock_quantity" min="0" step="1" required>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <?php while ($category = $categories->fetch_assoc()): ?>
                <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="currency_id">Currency</label>
        <select id="currency_id" name="currency_id" required>
            <?php while ($currency = $currencies->fetch_assoc()): ?>
                <option value="<?php echo $currency['currency_id']; ?>"><?php echo $currency['currency_code']; ?> (<?php echo $currency['symbol']; ?>)</option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="add_product">Add Product</button>
    </form>

    <h3>Product List</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock Quantity</th>
                <th>Category</th>
                <th>Currency</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo "₱" . number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['stock_quantity']; ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['currency_code']) . " (" . $product['symbol'] . ")"; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>">Edit</a> |
                        <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
session_start();
include('Mysqlconnection.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM product_history ORDER BY action_timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product History Log</title>
    <style>
        body {
            background-color: #e2f0d9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .top-nav {
            text-align: center;
            margin-bottom: 20px;
        }

        .top-nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #2f4f2f;
            font-weight: bold;
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 95%;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th {
            background-color: #a9d18e;
            color: #333;
            padding: 12px;
        }

        td {
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        .currency-symbol {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>Product History Log</h2>
    <div class="top-nav">
        <a href="admin_dashboard.php">← Back to Dashboard</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>History ID</th>
                <th>Product ID</th>
                <th>Action</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Category ID</th>
                <th>Currency ID</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['history_id']) ?></td>
                        <td><?= htmlspecialchars($row['product_id']) ?></td>
                        <td><?= htmlspecialchars($row['action']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>₱<?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['stock_quantity'] ?></td>
                        <td><?= $row['category_id'] ?></td>
                        <td><?= $row['currency_id'] ?></td>
                        <td><?= $row['action_timestamp'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10">No history records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

<?php $conn->close(); ?>

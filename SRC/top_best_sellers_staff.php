<?php
session_start();
include('Mysqlconnection.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CALL get_best_selling_products()";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Top Selling Products</title>
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
            width: 80%;
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
    </style>
</head>
<body>

    <h2>Top Selling Products</h2>
    <div class="top-nav">
        <a href="staff_dashboard.php">← Back to Dashboard</a>
        <a href="view_total_sales_staff.php">View Total Sales</a>
    </div>

    <table>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Total Quantity Sold</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['total_quantity_sold']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No best-selling data available.</td></tr>";
        }

        $conn->next_result(); // Clear remaining results
        ?>
    </table>

</body>
</html>
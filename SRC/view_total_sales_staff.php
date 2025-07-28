<?php
session_start();
include('Mysqlconnection.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CALL get_total_sales()";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Total Sales Summary</title>
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

        .currency-symbol {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>Total Sales Summary</h2>
    <div class="top-nav">
        <a href="staff_dashboard.php">← Back to Dashboard</a>
        <a href="top_best_sellers_staff.php">View Top Products</a>
    </div>

    <table>
        <tr>
            <th>Currency</th>
            <th>Total Sales</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['currency_code']) . "</td>";
                echo "<td>" . number_format($row['total_sales'], 2) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No sales data available.</td></tr>";
        }

        $conn->next_result(); // Clear remaining results
        ?>
    </table>

</body>
</html>
<?php
include 'Mysqlconnection.php'; // Your DB connection

// Fetch all logs
$query = "SELECT * FROM User_Transaction_Log ORDER BY action_timestamp DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Transaction Log</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #e6f4d6;
            text-align: center;
        }
        h1 {
            margin-top: 30px;
        }
        a {
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
            color: #2c5f2d;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 90%;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        th, td {
            padding: 12px 16px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #b6d7a8;
        }
    </style>
</head>
<body>
    <h1>User Transaction Log</h1>
    <div>
        <a href="manage_users.php">← Back to Manage Users</a>
        <a href="admin_dashboard.php">Dashboard</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Log ID</th>
                <th>User ID</th>
                <th>Action</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['log_id']); ?></td>
                    <td><?= htmlspecialchars($row['user_id']); ?></td>
                    <td><?= htmlspecialchars($row['action_type']); ?></td>
                    <td><?= htmlspecialchars($row['action_timestamp']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No transaction logs available.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
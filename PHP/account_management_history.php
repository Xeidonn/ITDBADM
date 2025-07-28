<?php
// manage_users.php
 session_start();
    include('Mysqlconnection.php');


$currentAdminId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

$deleted = [];
$sql = "SELECT audit_id, deleted_user_id, username, email, deleted_at 
        FROM Audit_Deleted_Users
        ORDER BY deleted_at DESC";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $deleted[] = $row;
    }
    $result->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Management History</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #e6f4d6;
            text-align: center;
        }
        h1 { margin-top: 30px; }
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
    <h1>Account Management History</h1>
    <div>
        <a href="manage_users.php">← Back to Manage Users</a>
        <a href="admin_dashboard.php">Dashboard</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Audit ID</th>
                <th>Deleted User ID</th>
                <th>Username (at deletion)</th>
                <th>Email (at deletion)</th>
                <th>Deleted At</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($deleted)): ?>
            <tr><td colspan="5">No deleted accounts recorded.</td></tr>
        <?php else: ?>
            <?php foreach ($deleted as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['audit_id']) ?></td>
                    <td><?= htmlspecialchars($row['deleted_user_id']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['deleted_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
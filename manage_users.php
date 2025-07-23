<?php
// manage_users.php
 session_start();
    include('Mysqlconnection.php');


$currentAdminId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $userIdToDelete = (int)$_POST['delete_user_id'];

    // SAFETY: Prevent admin deleting self
    if ($userIdToDelete === $currentAdminId) {
        // Optionally flash a message
        $_SESSION['manage_users_msg'] = "You cannot delete your own admin account.";
    } else {
        // Call stored procedure to delete user
        if ($stmt = $conn->prepare("CALL delete_user_by_id(?)")) {
            $stmt->bind_param("i", $userIdToDelete);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Redirect to avoid resubmission & show message
    header("Location: manage_users.php");
    exit();
}

// Fetch users via stored procedure
$users = [];
if ($result = $conn->query("CALL get_all_users()")) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->close();
    // Important when using multiple CALLS in same request
    while ($conn->more_results() && $conn->next_result()) { /* flush */ }
}

// Grab flash message (if any)
$msg = "";
if (isset($_SESSION['manage_users_msg'])) {
    $msg = $_SESSION['manage_users_msg'];
    unset($_SESSION['manage_users_msg']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #e6f4d6;
            text-align: center;
        }
        h1 { margin-top: 30px; }
        .top-links {
            margin: 10px 0 20px 0;
        }
        .top-links a {
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
            color: #2c5f2d;
        }
        .flash {
            margin: 10px auto;
            padding: 10px 16px;
            max-width: 600px;
            border-radius: 6px;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 90%;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 16px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #b6d7a8;
        }
        form {
            margin: 0;
        }
        button {
            padding: 6px 12px;
            background-color: #d9534f;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #c9302c;
        }
        /* disabled delete styling */
        .btn-disabled {
            background-color: #aaa !important;
            cursor: not-allowed !important;
        }
    </style>
</head>
<body>
    <h1>Manage Users</h1>
    <div class="top-links">
        <a href="admin_dashboard.php">← Back to Dashboard</a>
        <a href="account_management_history.php">Account Management History</a>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="flash"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="6">No users found.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <?php
                    $uid = (int)$user['user_id'];
                    $isCurrentAdmin = ($uid === $currentAdminId);
                ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role_name']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td>
                        <?php if ($isCurrentAdmin): ?>
                            <button type="button" class="btn-disabled" disabled>Cannot Delete Self</button>
                        <?php else: ?>
                            <form method="POST" onsubmit="return confirm('Delete this user?');">
                                <input type="hidden" name="delete_user_id" value="<?= $uid ?>">
                                <button type="submit">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
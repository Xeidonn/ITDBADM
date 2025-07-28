<?php
session_start();
include('Mysqlconnection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user data from the database
$username = $_SESSION['username'];
$sql = "SELECT * FROM Users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $message = "";

    // Fetch the current password hash from the database
    $sql = "SELECT password FROM Users WHERE username = '$username'";
    $result = $conn->query($sql);
    $user_data = $result->fetch_assoc();

    // Check if current password is correct
    if (password_verify($current_password, $user_data['password'])) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE Users SET password = '$hashed_password' WHERE username = '$username'";

            if ($conn->query($update_sql)) {
                $message = "Password changed successfully.";
            } else {
                $message = "Error updating password.";
            }
        } else {
            $message = "New password and confirmation do not match.";
        }
    } else {
        $message = "Current password is incorrect.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account | XD Hobby Shop</title>
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
        <?php endif; ?>
        </nav>
    </header>

    <section class="content">
        <h3>Your Account Details</h3>
        <div class="account-details">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Joined On:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
        </div>

        <!-- Message after password change -->
        <?php if (isset($message)): ?>
            <p><strong><?php echo $message; ?></strong></p>
        <?php endif; ?>

        <!-- Form to Change Password -->
        <form action="user_account.php" method="POST">
            <h4>Change Your Password</h4>
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" name="change_password">Change Password</button>
        </form>
    </section>
</body>
</html>
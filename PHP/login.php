<?php
session_start();
include('Mysqlconnection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch the user from the database
    $sql = "SELECT * FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // User is successfully logged in
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];

        // Clear the cart for the new user (if any) in localStorage
        echo "<script>
            localStorage.removeItem('cart');  // Clear the previous cart data on new login
            window.location.href = 'index.php';  // Redirect to homepage or user dashboard
        </script>";

        // Redirect to the appropriate page based on role
        if ($user['role_id'] == 1) {
            // Admin: Redirect to admin dashboard
            header("Location: admin_dashboard.php");
        } elseif ($user['role_id'] == 2) {
            // Staff: Redirect to staff dashboard
            header("Location: staff_dashboard.php");
        } elseif ($user['role_id'] == 3) {
            // Customer: Redirect to customer dashboard or homepage
            header("Location: index.php");
        }
    } else {
        echo "<script>alert('Invalid credentials. Please try again.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>
        <button type="button" onclick="window.history.back();">Back</button>
    </form>

    <p>OR</p>

    <!-- Link to the Admin Login Page -->
    <p><a href="admin_login.php">Login as Administrator</a></p>
</body>
</html>

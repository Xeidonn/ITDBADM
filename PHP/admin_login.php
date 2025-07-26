<?php
    include('Mysqlconnection.php');

    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if the user is an admin
        $sql = "SELECT * FROM Users WHERE email = ? AND role_id = 1";  // role_id = 1 is for admin
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            header("Location: admin_dashboard.php");  // Redirect to Admin Dashboard
        } else {
            echo "Invalid credentials or you are not an administrator.";
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
    <title>Admin Login</title>
    <link rel="stylesheet" href="../CSS/main.css">
</head>
<body>
    <h2>Login as Administrator</h2>
    <form method="POST" action="admin_login.php">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="admin@gmail.com" readonly><br>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>
        <button type="button" onclick="window.history.back();">Back</button>
    </form>
</body>
</html>

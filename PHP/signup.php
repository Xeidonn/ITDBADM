<?php
    include('Mysqlconnection.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password
        $role_name = $_POST['role']; // Get the selected role

        // Get the role_id based on the selected role (either customer or staff)
        $sql = "SELECT role_id FROM Roles WHERE role_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $role_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $role = $result->fetch_assoc();
        $role_id = $role['role_id']; // Get the role_id corresponding to the selected role

        // Insert the new user with the correct role_id
        $sql = "INSERT INTO Users (username, email, password, role_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $email, $password, $role_id);

        if ($stmt->execute()) {
            echo "Signup successful!";
            header("Location: login.php"); // Redirect to login after successful signup
        } else {
            echo "Error: " . $stmt->error;
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
    <title>Signup</title>
    <link rel="stylesheet" href="../CSS/main.css">
</head>
<body>
    <h2>Sign Up</h2>
    <form method="POST" action="signup.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required><br>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required><br>

        <label for="role">Select Role</label>
        <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="customer">Customer</option>
            <option value="staff">Staff</option>
        </select><br>

        <button type="submit">Sign Up</button>
    </form>
</body>
</html>

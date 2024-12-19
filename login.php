<?php
require 'config/config.php'; // Database connection
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($email) || empty($password)) {
        echo "Email and password are required.";
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['banned']) {
            echo "Your account has been banned.";
            exit();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] === 'admin') {
            header("Location: views/admin_incident_reports.php");
        } else {
            header("Location: views/home.php");
        }
        exit();
    } else {
        echo "Invalid email or password.";
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Login</title>
</head>
<body>
    <div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
        <h1 class="text-2xl font-bold mb-5">Login</h1>
        <form action="login.php" method="post">
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full border rounded p-2" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
        </form>
        <div class="mt-4">
            <a href="views/forgot_password.php" class="text-blue-500">Forgot Password?</a>
        </div>
    </div>
</body>
</html>

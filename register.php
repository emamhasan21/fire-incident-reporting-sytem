<?php
require 'config/config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];
    $nid_photo = $_FILES['nid_photo'];

    // Validate inputs
    if (empty($first_name) || empty($surname) || empty($email) || empty($phone_number) || empty($password) || empty($nid_photo['name'])) {
        echo "All fields are required.";
        exit();
    }

    // Check for valid email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "Email already registered.";
        exit();
    }
    $stmt->close();

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Handle NID Photo upload
    $target_dir = "assets/uploads/";
    $target_file = $target_dir . basename($nid_photo['name']);
    if (!move_uploaded_file($nid_photo['tmp_name'], $target_file)) {
        echo "Failed to upload NID photo.";
        exit();
    }

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (first_name, surname, email, phone_number, nid_photo, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $surname, $email, $phone_number, $target_file, $password_hash);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
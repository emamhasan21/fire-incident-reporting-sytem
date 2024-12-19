<?php
require '../config/config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE reports SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $report_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

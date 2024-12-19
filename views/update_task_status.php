<?php
session_start();
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $status = $_POST['status'];
    $fire_unit_id = $_SESSION['fire_unit_id'];

    if ($status === 'done') {
        $stmt = $conn->prepare("UPDATE fire_units SET current_task = NULL, current_latitude = latitude, current_longitude = longitude WHERE id = ?");
        $stmt->bind_param("i", $fire_unit_id);
        $stmt->execute();
        $stmt->close();
    } else if ($status === 'request_support') {
        // Handle support request logic here
    }

    $stmt = $conn->prepare("UPDATE reports SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $task_id);
    $stmt->execute();
    $stmt->close();

    header("Location: fire_unit_dashboard.php");
    exit();
}
?>

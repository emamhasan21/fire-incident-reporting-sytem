<?php
session_start();
require '../config/config.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'];
    $fire_unit_id = $_POST['fire_unit_id'];

    // Update the report with the assigned fire unit
    $stmt = $conn->prepare("UPDATE reports SET assigned_fire_unit = ?, status = 'assigned' WHERE id = ?");
    $stmt->bind_param("ii", $fire_unit_id, $report_id);
    $stmt->execute();
    $stmt->close();

    // Update the fire unit's current task
    $stmt = $conn->prepare("UPDATE fire_units SET current_task = ? WHERE id = ?");
    $stmt->bind_param("ii", $report_id, $fire_unit_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_incident_reports.php");
    exit();
}
?>

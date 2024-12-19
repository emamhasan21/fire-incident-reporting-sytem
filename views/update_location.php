<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['fire_unit_id'])) {
    header("Location: login_fire_unit.php");
    exit();
}

$fire_unit_id = $_SESSION['fire_unit_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];

    $stmt = $conn->prepare("UPDATE fire_units SET current_latitude = ?, current_longitude = ? WHERE id = ?");
    $stmt->bind_param("ddi", $latitude, $longitude, $fire_unit_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    $stmt->close();
    exit();
}
?>

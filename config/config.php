<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "incident_report_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Twilio configuration
$twilio_sid = '';
$twilio_token = '';
$twilio_phone_number = '';
?>

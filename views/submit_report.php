<?php
session_start();
require '../config/config.php';

$submission_successful = false;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function getNearestFireUnit($latitude, $longitude, $conn) {
    $sql = "SELECT id, name, 
            (6371 * acos(cos(radians(?)) * cos(radians(current_latitude)) * cos(radians(current_longitude) - radians(?)) + sin(radians(?)) * sin(radians(current_latitude)))) AS distance
            FROM fire_units
            WHERE current_task = 'available'
            ORDER BY distance
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddd", $latitude, $longitude, $latitude);
    $stmt->execute();
    $result = $stmt->get_result();
    $nearest_unit = $result->fetch_assoc();
    $stmt->close();
    return $nearest_unit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $description = $_POST['description'];
    $gps_location = $_POST['gps_location'];
    $casualties = $_POST['casualties'];

    // Handle file upload
    $image_video = $_FILES['image_video']['name'];
    $target_dir = "../assets/uploads/";
    $target_file = $target_dir . basename($image_video);
    move_uploaded_file($_FILES['image_video']['tmp_name'], $target_file);

    list($lat, $lon) = explode(',', $gps_location);

    // Check for existing reports within 30 minutes and 30 meters
    $stmt = $conn->prepare("
        SELECT id, ( 6371000 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance 
        FROM reports 
        WHERE timestamp > (NOW() - INTERVAL 30 MINUTE) 
        HAVING distance < 30 
        ORDER BY distance 
        LIMIT 1");
    $stmt->bind_param("ddd", $lat, $lon, $lat);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_report = $result->fetch_assoc();

    if ($existing_report) {
        // Mark as subreport
        $main_report_id = $existing_report['id'];
    } else {
        // This is a main report
        $main_report_id = NULL;
    }
    $stmt->close();

    // Find the nearest available fire unit
    $nearest_unit = getNearestFireUnit($lat, $lon, $conn);
    $assigned_unit_id = $nearest_unit ? $nearest_unit['id'] : NULL;

    // Insert the new report
    $stmt = $conn->prepare("
        INSERT INTO reports (user_id, description, image_video, latitude, longitude, casualties, main_report_id, assigned_fire_unit) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssi", $user_id, $description, $target_file, $lat, $lon, $casualties, $main_report_id, $assigned_fire_unit);
    $stmt->execute();
    $report_id = $stmt->insert_id; // Get the ID of the newly inserted report
    $stmt->close();

    // Update the fire unit's current task to 'assigned' if a unit was assigned
    if ($assigned_unit_id) {
        $stmt = $conn->prepare("UPDATE fire_units SET current_task = 'assigned', current_latitude = ?, current_longitude = ? WHERE id = ?");
        $stmt->bind_param("ddi", $lat, $lon, $assigned_unit_id);
        $stmt->execute();
        $stmt->close();
    }

    // Send the report details via WebSocket
    $ws_data = json_encode([
        'type' => 'new_report',
        'report_id' => $report_id,
        'latitude' => $lat,
        'longitude' => $lon,
        'description' => $description,
        'casualties' => $casualties,
        'image_video' => $target_file
    ]);

    echo "<script>
        const socket = new WebSocket('ws://localhost:8080/gps');
        socket.onopen = function() {
            socket.send('$ws_data');
        };
    </script>";

    $submission_successful = true;
    // header("Location: home.php");
    // exit();
}

$title = "Submit Report";
include 'header.php';
?>

<div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">Submit Incident Report</h1>
    <?php if ($submission_successful): ?>
        <div class="text-green-500 mb-4">Report submitted successfully! You will be redirected to the home page in 10 seconds.</div>
        <script>
            setTimeout(function() {
                window.location.href = 'home.php';
            }, 10000); // 10 seconds
        </script>
    <?php else: ?>
        <form action="submit_report.php" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700">Description</label>
                <textarea name="description" class="w-full border rounded p-2" required></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Image/Video</label>
                <input type="file" name="image_video" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">GPS Location</label>
                <div class="flex flex-col sm:flex-row">
                    <input type="text" id="gps_location" name="gps_location" class="w-full border rounded p-2 mb-2 sm:mb-0" readonly required>
                    <button type="button" onclick="getLocation()" class="sm:ml-2 bg-blue-500 text-white p-2 rounded">Pick Location</button>
                </div>
            </div>
            <div id="map" class="h-64 w-full mb-4"></div>
            <div class="mb-4">
                <label class="block text-gray-700">Casualties</label>
                <select name="casualties" class="w-full border rounded p-2" required>
                    <option value="none">None</option>
                    <option value="minor">Minor</option>
                    <option value="major">Major</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Submit</button>
        </form>
    <?php endif; ?>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let map;
let marker;

function initMap() {
    map = L.map('map').setView([51.505, -0.09], 13); // Default location and zoom level
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lon = e.latlng.lng;
        document.getElementById('gps_location').value = `${lat},${lon}`;
        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }
    });
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;
    document.getElementById('gps_location').value = `${lat},${lon}`;
    if (map) {
        map.setView([lat, lon], 13);
        if (marker) {
            marker.setLatLng([lat, lon]);
        } else {
            marker = L.marker([lat, lon]).addTo(map);
        }
    }
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            alert("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            alert("An unknown error occurred.");
            break;
    }
}

document.addEventListener('DOMContentLoaded', initMap);
</script>
<?php include 'footer.php'; ?>

<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['fire_unit_id'])) {
    header("Location: login_fire_unit.php");
    exit();
}

$fire_unit_id = $_SESSION['fire_unit_id'];

// Fetch fire unit details
$stmt = $conn->prepare("SELECT * FROM fire_units WHERE id = ?");
$stmt->bind_param("i", $fire_unit_id);
$stmt->execute();
$fire_unit = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch current task details
$task = null;
if ($fire_unit['current_task']) {
    $stmt = $conn->prepare("SELECT * FROM reports WHERE id = ?");
    $stmt->bind_param("i", $fire_unit['current_task']);
    $stmt->execute();
    $task = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$title = "Fire Unit Dashboard";
include 'header.php';
?>
<div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">Fire Unit Dashboard</h1>
    <div class="mb-4">
        <label class="block text-gray-700">Current Location</label>
        <div id="map" class="h-64 w-full mb-4"></div>
    </div>
    <?php if ($task) { ?>
    <div class="mb-4">
        <h2 class="text-xl font-bold mb-2">Current Task</h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($task['latitude'] . ', ' . $task['longitude']); ?></p>
        <form action="update_task_status.php" method="post">
            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
            <button type="submit" name="status" value="done" class="bg-green-500 text-white px-4 py-2 rounded mt-2">Mark as Done</button>
            <button type="submit" name="request_support" class="bg-red-500 text-white px-4 py-2 rounded mt-2">Request Support</button>
        </form>
    </div>
    <?php } else { ?>
    <p>No current tasks assigned.</p>
    <?php } ?>
</div>

<style>
/* Hide the directions menu */
.leaflet-routing-container-hide {
    display: none;
}
</style>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
<script>
let map;
let marker;
let routingControl;
const unitId = <?php echo json_encode($fire_unit_id); ?>;
const defaultLatitude = <?php echo json_encode($fire_unit['latitude']); ?>;
const defaultLongitude = <?php echo json_encode($fire_unit['longitude']); ?>;
const currentLatitude = <?php echo json_encode($fire_unit['current_latitude'] ?? $fire_unit['latitude']); ?>;
const currentLongitude = <?php echo json_encode($fire_unit['current_longitude'] ?? $fire_unit['longitude']); ?>;
const task = <?php echo json_encode($task); ?>;

function initMap() {
    map = L.map('map').setView([currentLatitude, currentLongitude], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    marker = L.marker([currentLatitude, currentLongitude]).addTo(map);

    if (task) {
        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(currentLatitude, currentLongitude),
                L.latLng(task.latitude, task.longitude)
            ],
            routeWhileDragging: true,
            createMarker: function() { return null; }, // Hide routing markers
        }).addTo(map);

        // Hide the routing menu
        document.querySelector('.leaflet-routing-container').classList.add('leaflet-routing-container-hide');
    }
}

document.addEventListener('DOMContentLoaded', initMap);

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            fetch('update_location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ latitude: lat, longitude: lon })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    marker.setLatLng([lat, lon]);
                    map.setView([lat, lon], 13);
                    if (routingControl) {
                        routingControl.setWaypoints([
                            L.latLng(lat, lon),
                            L.latLng(task.latitude, task.longitude)
                        ]);
                    } else if (task) {
                        routingControl = L.Routing.control({
                            waypoints: [
                                L.latLng(lat, lon),
                                L.latLng(task.latitude, task.longitude)
                            ],
                            routeWhileDragging: true,
                            createMarker: function() { return null; }, // Hide routing markers
                        }).addTo(map);

                        // Hide the routing menu
                        document.querySelector('.leaflet-routing-container').classList.add('leaflet-routing-container-hide');
                    }
                } else {
                    alert('Failed to update location.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }, showError);
    } else {
        alert("Geolocation is not supported by this browser.");
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

// Update location every 10 seconds
setInterval(getCurrentLocation, 10000);
</script>
<?php include 'footer.php'; ?>

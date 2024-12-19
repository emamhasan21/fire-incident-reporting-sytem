<?php
session_start();
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $fire_station_id = $_POST['fire_station_id'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Generate username
    $result = $conn->query("SELECT COUNT(*) as count FROM fire_units");
    $count = $result->fetch_assoc()['count'] + 1;
    $username = 'fhunit_' . str_pad($count, 3, '0', STR_PAD_LEFT);

    $stmt = $conn->prepare("INSERT INTO fire_units (username, password, name, fire_station_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $username, $password, $name, $fire_station_id);
    $stmt->execute();
    $stmt->close();

    header("Location: login_fire_unit.php");
    exit();
}

$fire_stations = $conn->query("SELECT * FROM fire_stations");

$title = "Register Fire Unit";
include 'header.php';
?>
<div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">Register Fire Unit</h1>
    <form action="register_fire_unit.php" method="post">
        <div class="mb-4">
            <label class="block text-gray-700">Unit Name</label>
            <input type="text" name="name" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Fire Station</label>
            <select name="fire_station_id" class="w-full border rounded p-2" required>
                <?php while ($station = $fire_stations->fetch_assoc()): ?>
                    <option value="<?php echo $station['id']; ?>"><?php echo $station['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Password</label>
            <input type="password" name="password" class="w-full border rounded p-2" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Register</button>
    </form>
</div>
<?php include 'footer.php'; ?>

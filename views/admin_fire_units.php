<?php
session_start();
require '../config/config.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$fire_units = $conn->query("SELECT * FROM fire_units");

$title = "Admin - Manage Fire Units";
include 'header.php';
?>
<div class="container mx-auto my-10">
    <h1 class="text-2xl font-bold mb-5">Manage Fire Units</h1>
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Username</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Fire Station</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($unit = $fire_units->fetch_assoc()): ?>
                <tr>
                    <td class="border p-2"><?php echo $unit['id']; ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($unit['username']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($unit['name']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($unit['fire_station_id']); ?></td>
                    <td class="border p-2">
                        <!-- Add actions such as editing or deleting fire units -->
                        <a href="edit_fire_unit.php?unit_id=<?php echo $unit['id']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded">Edit</a>
                        <a href="delete_fire_unit.php?unit_id=<?php echo $unit['id']; ?>" class="bg-red-500 text-white px-4 py-2 rounded">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>

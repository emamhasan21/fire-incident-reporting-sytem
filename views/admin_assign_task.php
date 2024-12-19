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

    $stmt = $conn->prepare("UPDATE reports SET assigned_fire_unit = ?, status = 'assigned' WHERE id = ?");
    $stmt->bind_param("ii", $fire_unit_id, $report_id);
    $stmt->execute();
    $stmt->close();

    // Update fire_unit's current task
    $stmt = $conn->prepare("UPDATE fire_units SET current_task = ? WHERE id = ?");
    $stmt->bind_param("ii", $report_id, $fire_unit_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_incident_reports.php");
    exit();
}

$reports = $conn->query("SELECT * FROM reports WHERE status = 'unassigned'");
$fire_units = $conn->query("SELECT * FROM fire_units");

$title = "Admin - Assign Task";
include 'header.php';
?>
<div class="container mx-auto my-10">
    <h1 class="text-2xl font-bold mb-5">Assign Task to Fire Unit</h1>
    <form action="admin_assign_task.php" method="post">
        <div class="mb-4">
            <label class="block text-gray-700">Report</label>
            <select name="report_id" class="w-full border rounded p-2" required>
                <?php while ($report = $reports->fetch_assoc()): ?>
                    <option value="<?php echo $report['id']; ?>"><?php echo htmlspecialchars($report['description']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Fire Unit</label>
            <select name="fire_unit_id" class="w-full border rounded p-2" required>
                <?php while ($unit = $fire_units->fetch_assoc()): ?>
                    <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Assign</button>
    </form>
</div>
<?php include 'footer.php'; ?>

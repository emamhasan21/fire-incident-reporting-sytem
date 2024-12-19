<?php
session_start();
require '../config/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Connect to the database
$conn = new mysqli("localhost", "root", "", "incident_report_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the user's report history
$sql = "SELECT * FROM reports WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reports = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$title = "Report History";
include 'header.php';
?>
<div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">Your Report History</h1>
    <?php if (count($reports) > 0): ?>
        <table class="w-full border">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Report ID</th>
                    <th class="border px-4 py-2">Date</th>
                    <th class="border px-4 py-2">Description</th>
                    <th class="border px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($report['id']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($report['timestamp']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($report['description']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($report['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no submitted reports.</p>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>

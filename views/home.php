<?php
session_start();
require '../config/config.php';

$title = "Home";
include 'header.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT first_name, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    $first_name = $user['first_name'];
    $role = $user['role'];
} else {
    $first_name = null;
    $role = null;
}
?>

<div class="container mx-auto my-10">
    <?php if ($first_name && $role): ?>
        <h1 class="text-3xl font-bold mb-5">Welcome <?php echo htmlspecialchars($first_name); ?>, to <?php echo htmlspecialchars($role); ?> dashboard</h1>
        <?php if ($role === 'user'): ?>
            <div class="flex justify-center mt-10">
                <a href="submit_report.php" class="bg-red-500 text-white text-2xl font-bold py-4 px-6 rounded-full hover:bg-red-700 transition duration-300">Submit Report</a>
            </div>
            <div class="flex justify-center mt-5">
                <a href="tel:16103" class="bg-green-500 text-white text-2xl font-bold py-4 px-6 rounded-full hover:bg-green-700 transition duration-300">Emergency Call</a>
            </div>
        <?php elseif ($role === 'admin'): ?>
            <p class="text-xl">Please use the navigation menu to manage reports and users.</p>
        <?php elseif ($role === 'driver'): ?>
            <p class="text-xl">Please use the navigation menu to view your assigned tasks.</p>
        <?php endif; ?>
    <?php else: ?>
        <h1 class="text-3xl font-bold mb-5">Welcome to the Incident Report System</h1>
        <div class="mb-5">
            <a href="register_fire_unit.php" class="bg-blue-500 text-white px-4 py-2 rounded">Register Fire Unit</a>
        </div>
        <div class="mb-5">
            <a href="login_fire_unit.php" class="bg-blue-500 text-white px-4 py-2 rounded">Login as Fire Unit</a>
        </div>
        <div class="mb-5">
            <a href="login.php" class="bg-blue-500 text-white px-4 py-2 rounded">Login</a>
            <a href="register.php" class="bg-blue-500 text-white px-4 py-2 rounded">Register</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

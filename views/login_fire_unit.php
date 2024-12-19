<?php
session_start();
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM fire_units WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['fire_unit_id'] = $user['id'];
        header("Location: fire_unit_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}

$title = "Fire Unit Login";
include 'header.php';
?>
<div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">Fire Unit Login</h1>
    <?php if (isset($error)) { echo "<div class='text-red-500 mb-4'>$error</div>"; } ?>
    <form action="login_fire_unit.php" method="post">
        <div class="mb-4">
            <label class="block text-gray-700">Username</label>
            <input type="text" name="username" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Password</label>
            <input type="password" name="password" class="w-full border rounded p-2" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
    </form>
</div>
<?php include 'footer.php'; ?>

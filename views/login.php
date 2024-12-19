<?php
session_start();
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        if ($user['status'] === 'banned') {
            $_SESSION['login_error'] = "Your account has been banned. Please contact support.";
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header("Location: admin_incident_reports.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid email or password.";
        }
    } else {
        $_SESSION['login_error'] = "Invalid email or password.";
    }
}

$title = "Login";
include 'header.php';
?>
<div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">Login</h1>
    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="bg-red-500 text-white p-4 mb-4 rounded">
            <?php
            echo $_SESSION['login_error'];
            unset($_SESSION['login_error']);
            ?>
        </div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Password</label>
            <input type="password" name="password" class="w-full border rounded p-2" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
    </form>

    <div class="mt-4">
        <a href="forgot_password.php" class="text-blue-500">Forgot Password? </a>or
        <a href="register.php" class="text-blue-500">Register</a>
    </div>

</div>
<?php include 'footer.php'; ?>

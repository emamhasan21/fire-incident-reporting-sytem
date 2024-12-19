<?php
require '../config/config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // Validate email
    if (empty($email)) {
        echo "Email is required.";
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        // Generate reset token and send email (pseudo-code)
        $reset_token = bin2hex(random_bytes(16));
        $reset_url = "http://localhost/incident_report/views/reset_password.php?token=$reset_token";
        
        // Save reset token to database
        $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $reset_token, $email);
        $stmt->execute();
        
        // Send email to user with reset URL (pseudo-code)
        // mail($email, "Password Reset Request", "Click here to reset your password: $reset_url");

        echo "Password reset email sent. Check your inbox.";
    } else {
        echo "No user found with that email.";
    }

    $stmt->close();
    $conn->close();
}

$title = "Forgot Password";
include 'header.php';
?>
<div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">Forgot Password</h1>
    <form action="forgot_password.php" method="post">
        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" class="w-full border rounded p-2" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Submit</button>
    </form>
</div>
<?php include 'footer.php'; ?>

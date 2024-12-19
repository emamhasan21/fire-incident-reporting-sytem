<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, surname = ?, email = ?, phone_number = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $first_name, $surname, $email, $phone_number, $user_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['profile_update_success'] = "Your personal info updated successfully.";
    header("Location: profile.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$title = "Profile";
include 'header.php';
?>
<div class="container mx-auto my-10">
    <h1 class="text-2xl font-bold mb-5">Profile</h1>
    <?php if (isset($_SESSION['profile_update_success'])): ?>
        <div class="bg-green-500 text-white p-4 mb-4 rounded">
            <?php
            echo $_SESSION['profile_update_success'];
            unset($_SESSION['profile_update_success']);
            ?>
        </div>
    <?php endif; ?>
    <form action="profile.php" method="post">
        <div class="mb-4">
            <label class="block text-gray-700">First Name</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Last Name</label>
            <input type="text" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Phone</label>
            <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" class="w-full border rounded p-2" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Update Profile</button>
    </form>
</div>

<div class="container mx-auto my-10">
    <h1 class="text-2xl font-bold mb-5">Reset Password</h1>
    <form action="reset_password.php" method="post">
        <div class="mb-4">
            <label class="block text-gray-700">New Password</label>
            <input type="password" name="new_password" class="w-full border rounded p-2" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Reset Password</button>
    </form>
</div>

<?php include 'footer.php'; ?>
<?php
session_start();
require '../config/config.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $action = $_GET['action'];
    $new_status = ($action === 'ban') ? 'banned' : 'active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_users.php");
    exit();
}

$users = $conn->query("SELECT * FROM users");

$title = "Admin - Manage Users";
include 'header.php';
?>
<div class="container mx-auto my-10">
    <h1 class="text-2xl font-bold mb-5">Manage Users</h1>
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">First Name</th>
                <th class="border p-2">Last Name</th>
                <th class="border p-2">Email</th>
                <th class="border p-2">Role</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td class="border p-2"><?php echo $user['id']; ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($user['first_name']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($user['surname']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($user['role']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($user['status']); ?></td>
                    <td class="border p-2">
                        <?php if ($user['status'] === 'active'): ?>
                            <a href="?action=ban&user_id=<?php echo $user['id']; ?>" class="bg-red-500 text-white px-4 py-2 rounded">Ban</a>
                        <?php else: ?>
                            <a href="?action=unban&user_id=<?php echo $user['id']; ?>" class="bg-green-500 text-white px-4 py-2 rounded">Unban</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>

<?php
session_start();
require '../config/config.php'; // Database connection

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch admin's first name and surname
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name, surname FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($first_name, $surname);
$stmt->fetch();
$stmt->close();

$author = $first_name . ' ' . $surname;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $heading = $_POST['heading'];
    $body = $_POST['body'];
    $created_at = date('Y-m-d H:i:s');

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target_dir = "../assets/uploads/";
    $target_file = $target_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO news_feed (heading, author, body, image, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $heading, $author, $body, $image, $created_at);
        $stmt->execute();
        $stmt->close();
        header("Location: news_feed.php");
    } else {
        $error = "Failed to upload image.";
    }
}

$title = "Post News";
include 'header.php';
?>
<div class="max-w-lg mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">Post News</h1>
    <?php if (isset($error)) { echo "<div class='text-red-500 mb-4'>$error</div>"; } ?>
    <form action="post_news.php" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700">Heading</label>
            <input type="text" name="heading" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Image</label>
            <input type="file" name="image" class="w-full border rounded p-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Body</label>
            <textarea name="body" class="w-full border rounded p-2" rows="5" required></textarea>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Post</button>
    </form>
</div>
<?php include 'footer.php'; ?>

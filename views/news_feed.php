<?php
session_start();
require '../config/config.php'; // Database connection

// Fetch news posts
$news_posts = $conn->query("SELECT * FROM news_feed ORDER BY created_at DESC");

$title = "News Feed";
include 'header.php';
?>
<div class="max-w-3xl mx-auto my-10 bg-white p-5 rounded shadow-sm">
    <h1 class="text-2xl font-bold mb-5">News Feed</h1>
    <?php while ($post = $news_posts->fetch_assoc()) { ?>
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($post['heading']); ?></h2>
        <p class="text-gray-500 text-sm mb-2">Published on <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?> by <?php echo htmlspecialchars($post['author']); ?></p>
        <?php if ($post['image']) { ?>
        <img src="../assets/uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="mb-4 w-full">
        <?php } ?>
        <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($post['body'])); ?></p>
        <hr class="border-t-2 border-gray-300">
    </div>
    <?php } ?>
</div>
<?php include 'footer.php'; ?>

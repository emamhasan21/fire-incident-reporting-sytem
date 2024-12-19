<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <title><?php echo $title; ?></title>
    <style>
        #menu, #mobile-menu {
            transition: max-height 0.3s ease-in-out;
        }
        #mobile-menu.hidden {
            max-height: 0;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="home.php" class="text-white text-xl font-bold">FireHero</a>
            <div class="block lg:hidden">
                <button id="menu-btn" class="text-white focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="hidden lg:flex lg:items-center lg:space-x-4" id="menu">
                <a href="home.php" class="text-gray-300 hover:text-white px-3">Home</a>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <a href="submit_report.php" class="text-gray-300 hover:text-white px-3">Submit Report</a>
                    <a href="news_feed.php" class="text-gray-300 hover:text-white px-3">News Feed</a>
                    <a href="profile.php" class="text-gray-300 hover:text-white px-3">Profile</a>
                    <a href="report_history.php" class="text-gray-300 hover:text-white px-3">Reports</a>
                    <?php if ($_SESSION['role'] === 'admin') { ?>
                        <a href="admin_incident_reports.php" class="text-gray-300 hover:text-white px-3">Dashboard</a>
                    <?php } ?>
                    <a href="logout.php" class="text-gray-300 hover:text-white px-3">Logout</a>
                <?php } elseif (isset($_SESSION['fire_unit_id'])) { ?>
                    <a href="fire_unit_dashboard.php" class="text-gray-300 hover:text-white px-3">Dashboard</a>
                    <a href="logout.php" class="text-gray-300 hover:text-white px-3">Logout</a>
                <?php } else { ?>
                    <a href="news_feed.php" class="text-gray-300 hover:text-white px-3">News Feed</a>
                    <a href="login.php" class="text-gray-300 hover:text-white px-3">Login</a>
<!--                    <a href="register.php" class="text-gray-300 hover:text-white px-3">Register</a>
                    <a href="register_fire_unit.php" class="text-gray-300 hover:text-white px-3">Register Fire Unit</a>
                    <a href="login_fire_unit.php" class="text-gray-300 hover:text-white px-3">Login as Fire Unit</a> -->
                <?php } ?>
            </div>
        </div>
        <div class="lg:hidden hidden" id="mobile-menu">
            <a href="home.php" class="block text-gray-300 hover:text-white px-3 py-2">Home</a>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="submit_report.php" class="block text-gray-300 hover:text-white px-3 py-2">Submit Report</a>
                <a href="news_feed.php" class="block text-gray-300 hover:text-white px-3 py-2">News Feed</a>
                <a href="profile.php" class="block text-gray-300 hover:text-white px-3 py-2">Profile</a>
                <?php if ($_SESSION['role'] === 'admin') { ?>
                    <a href="admin_incident_reports.php" class="block text-gray-300 hover:text-white px-3 py-2">Dashboard</a>
                <?php } ?>
                <a href="logout.php" class="block text-gray-300 hover:text-white px-3 py-2">Logout</a>
            <?php } elseif (isset($_SESSION['fire_unit_id'])) { ?>
                <a href="fire_unit_dashboard.php" class="block text-gray-300 hover:text-white px-3 py-2">Dashboard</a>
                <a href="logout.php" class="block text-gray-300 hover:text-white px-3 py-2">Logout</a>
            <?php } else { ?>
                <a href="news_feed.php" class="block text-gray-300 hover:text-white px-3 py-2">News Feed</a>
                <a href="login.php" class="block text-gray-300 hover:text-white px-3 py-2">Login</a>
            <?php } ?>
        </div>
    </nav>

    <script>
        document.getElementById('menu-btn').addEventListener('click', function() {
            var menu = document.getElementById('mobile-menu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menu.style.maxHeight = menu.scrollHeight + "px";
            } else {
                menu.classList.add('hidden');
                menu.style.maxHeight = null;
            }
        });
    </script>
</body>
</html>

<?php
require '../config/config.php'; // Database connection

$users = $conn->query("SELECT * FROM users");

echo '<table class="min-w-full bg-white">
    <thead>
        <tr>
            <th class="py-2 px-4 border">ID</th>
            <th class="py-2 px-4 border">First Name</th>
            <th class="py-2 px-4 border">Surname</th>
            <th class="py-2 px-4 border">Email</th>
            <th class="py-2 px-4 border">Phone Number</th>
            <th class="py-2 px-4 border">Role</th>
            <th class="py-2 px-4 border">Banned</th>
            <th class="py-2 px-4 border">Actions</th>
        </tr>
    </thead>
    <tbody>';
while ($user = $users->fetch_assoc()) {
    echo '<tr>
        <td class="py-2 px-4 border">' . $user['id'] . '</td>
        <td class="py-2 px-4 border">' . $user['first_name'] . '</td>
        <td class="py-2 px-4 border">' . $user['surname'] . '</td>
        <td class="py-2 px-4 border">' . $user['email'] . '</td>
        <td class="py-2 px-4 border">' . $user['phone_number'] . '</td>
        <td class="py-2 px-4 border">' . $user['role'] . '</td>
        <td class="py-2 px-4 border">' . ($user['banned'] ? 'Yes' : 'No') . '</td>
        <td class="py-2 px-4 border">
            <form action="toggle_ban_user.php" method="post">
                <input type="hidden" name="user_id" value="' . $user['id'] . '">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">' . ($user['banned'] ? 'Unban' : 'Ban') . '</button>
            </form>
        </td>
    </tr>';
}
echo '</tbody></table>';
?>

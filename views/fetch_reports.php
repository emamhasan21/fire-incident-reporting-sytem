<?php
require '../config/config.php'; // Database connection

$reports = $conn->query("SELECT * FROM reports");

echo '<table class="min-w-full bg-white">
    <thead>
        <tr>
            <th class="py-2 px-4 border">ID</th>
            <th class="py-2 px-4 border">User ID</th>
            <th class="py-2 px-4 border">Description</th>
            <th class="py-2 px-4 border">GPS Location</th>
            <th class="py-2 px-4 border">Casualties</th>
            <th class="py-2 px-4 border">Status</th>
            <th class="py-2 px-4 border">Actions</th>
        </tr>
    </thead>
    <tbody>';
while ($report = $reports->fetch_assoc()) {
    echo '<tr>
        <td class="py-2 px-4 border">' . $report['id'] . '</td>
        <td class="py-2 px-4 border">' . $report['user_id'] . '</td>
        <td class="py-2 px-4 border">' . $report['description'] . '</td>
        <td class="py-2 px-4 border">' . $report['gps_location'] . '</td>
        <td class="py-2 px-4 border">' . $report['casualties'] . '</td>
        <td class="py-2 px-4 border">' . $report['status'] . '</td>
        <td class="py-2 px-4 border">
            <form action="update_report_status.php" method="post">
                <input type="hidden" name="report_id" value="' . $report['id'] . '">
                <select name="status" class="border rounded">
                    <option value="unassigned" ' . ($report['status'] == 'unassigned' ? 'selected' : '') . '>Unassigned</option>
                    <option value="assigned" ' . ($report['status'] == 'assigned' ? 'selected' : '') . '>Assigned</option>
                    <option value="done" ' . ($report['status'] == 'done' ? 'selected' : '') . '>Done</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
            </form>
        </td>
    </tr>';
}
echo '</tbody></table>';
?>

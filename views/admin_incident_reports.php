<?php
session_start();
require '../config/config.php'; // Database connection

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submission to update report status
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['report_id']) && isset($_POST['status'])) {
        $report_id = $_POST['report_id'];
        $status = $_POST['status'];
        
        // Perform update query
        $update_query = "UPDATE reports SET status = '$status' WHERE id = $report_id";
        if ($conn->query($update_query) === TRUE) {
            // Optional: Provide feedback that update was successful
            // echo "Status updated successfully";
        } else {
            // Handle error if update fails
            // echo "Error updating record: " . $conn->error;
        }
    }
}

// Fetch main reports
$main_reports = $conn->query("SELECT * FROM reports WHERE main_report_id IS NULL");

$title = "Incident Reports";
include 'header.php';
?>
<div class="flex flex-col lg:flex-row">
    <?php include 'admin_submenu.php'; ?>
    <div class="flex-grow p-5">
        <h1 class="text-3xl font-bold mb-5">Incident Reports</h1>
        <div id="reports_table">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border">ID</th>
                        <th class="py-2 px-4 border">User ID</th>
                        <th class="py-2 px-4 border">Description</th>
                        <th class="py-2 px-4 border">GPS Location</th>
                        <th class="py-2 px-4 border">Casualties</th>
                        <th class="py-2 px-4 border">Status</th>
                        <th class="py-2 px-4 border">Fire Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($main_report = $main_reports->fetch_assoc()) { ?>
                    <tr>
                        <td class="py-2 px-4 border"><?php echo $main_report['id']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $main_report['user_id']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $main_report['description']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $main_report['latitude'] . ', ' . $main_report['longitude']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $main_report['casualties']; ?></td>
                        <td class="py-2 px-4 border">
                            <form action="admin_incident_reports.php" method="post"> <!-- Updated action -->
                                <input type="hidden" name="report_id" value="<?php echo $main_report['id']; ?>">
                                <select name="status" class="border rounded" onchange="this.form.submit()">
                                    <option value="unassigned" <?php echo $main_report['status'] == 'unassigned' ? 'selected' : ''; ?>>Unassigned</option>
                                    <option value="assigned" <?php echo $main_report['status'] == 'assigned' ? 'selected' : ''; ?>>Assigned</option>
                                    <option value="done" <?php echo $main_report['status'] == 'done' ? 'selected' : ''; ?>>Done</option>
                                </select>
                            </form>
                        </td>
                        <td class="py-2 px-4 border">
                            <?php if ($main_report['status'] == 'done') { ?>
                                <span class="text-gray-500">No fire unit assigned</span>
                            <?php } else if ($main_report['status'] == 'assigned') { ?>
                                <span class="bg-green-500 text-white px-4 py-2 rounded">Assigned</span>
                            <?php } else { ?>
                                <form action="assign_fire_unit.php" method="post">
                                    <input type="hidden" name="report_id" value="<?php echo $main_report['id']; ?>">
                                    <select name="fire_unit_id" class="border rounded">
                                        <?php
                                        // Fetch available fire units
                                        $fire_units = $conn->query("SELECT * FROM fire_units WHERE current_task IS NULL");
                                        while ($fire_unit = $fire_units->fetch_assoc()) { ?>
                                        <option value="<?php echo $fire_unit['id']; ?>"><?php echo $fire_unit['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Assign</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    // Fetch subreports for the main report
                    $subreports = $conn->query("SELECT * FROM reports WHERE main_report_id = " . $main_report['id']);
                    while ($subreport = $subreports->fetch_assoc()) { ?>
                    <tr class="bg-gray-100">
                        <td class="py-2 px-4 border"><?php echo $subreport['id']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $subreport['user_id']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $subreport['description']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $subreport['latitude'] . ', ' . $subreport['longitude']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $subreport['casualties']; ?></td>
                        <td class="py-2 px-4 border">
                            <form action="admin_incident_reports.php" method="post"> <!-- Updated action -->
                                <input type="hidden" name="report_id" value="<?php echo $subreport['id']; ?>">
                                <select name="status" class="border rounded" onchange="this.form.submit()">
                                    <option value="unassigned" <?php echo $subreport['status'] == 'unassigned' ? 'selected' : ''; ?>>Unassigned</option>
                                    <option value="assigned" <?php echo $subreport['status'] == 'assigned' ? 'selected' : ''; ?>>Assigned</option>
                                    <option value="done" <?php echo $subreport['status'] == 'done' ? 'selected' : ''; ?>>Done</option>
                                </select>
                            </form>
                        </td>
                        <td class="py-2 px-4 border">
                            <?php if ($subreport['status'] == 'done') { ?>
                                <span class="text-gray-500">No fire unit assigned</span>
                            <?php } else if ($subreport['status'] == 'assigned') { ?>
                                <span class="bg-green-500 text-white px-4 py-2 rounded">Assigned</span>
                            <?php } else { ?>
                                <form action="assign_fire_unit.php" method="post">
                                    <input type="hidden" name="report_id" value="<?php echo $subreport['id']; ?>">
                                    <select name="fire_unit_id" class="border rounded">
                                        <?php
                                        // Fetch available fire units
                                        $fire_units = $conn->query("SELECT * FROM fire_units WHERE current_task IS NULL");
                                        while ($fire_unit = $fire_units->fetch_assoc()) { ?>
                                        <option value="<?php echo $fire_unit['id']; ?>"><?php echo $fire_unit['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Assign</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

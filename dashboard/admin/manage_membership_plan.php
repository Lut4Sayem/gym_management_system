<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "GYM_MANAGEMENT_SYSTEM";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_id = $_POST['plan_id'];
    $name = $_POST['name'];
    $duration = $_POST['duration'];
    $amount = $_POST['amount'];
    $created_by = $_POST['created_by'];

    if (isset($_POST['update'])) {
        // Update query
        $sql = "UPDATE membership_plan SET Name='$name', Duration='$duration', Amount='$amount', Created_by='$created_by' WHERE Plan_id='$plan_id'";
    } else {
        // Insert query
        $sql = "INSERT INTO membership_plan (Plan_id, Name, Duration, Amount, Created_by) VALUES ('$plan_id', '$name', '$duration', '$amount', '$created_by')";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Membership plan saved successfully.</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM membership_plan WHERE Plan_id='$delete_id'";
    $conn->query($sql);
}

// Fetch all membership plans
$result = $conn->query("SELECT * FROM membership_plan");
// If edit is triggered
$edit_plan = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM membership_plan WHERE Plan_id='$edit_id'");
    $edit_plan = $edit_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Membership Plans</title>
    <script>
        function confirmDelete(planId) {
            if (confirm("Are you sure you want to delete this plan?")) {
                window.location.href = '?delete=' + planId;
            }
        }
    </script>
</head>
<body>
    <h2>Manage Membership Plans</h2>

    <h3><?php echo $edit_plan ? "Update Membership Plan" : "Add New Membership Plan"; ?></h3>
    <form method="POST">
        <label>Plan ID:</label><br>
        <input type="number" name="plan_id" value="<?php echo $edit_plan['Plan_id'] ?? ''; ?>" required <?php echo $edit_plan ? 'readonly' : ''; ?>><br><br>

        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo $edit_plan['Name'] ?? ''; ?>" required><br><br>

        <label>Duration (Days):</label><br>
        <input type="number" name="duration" value="<?php echo $edit_plan['Duration'] ?? ''; ?>" required><br><br>

        <label>Amount:</label><br>
        <input type="number" step="0.01" name="amount" value="<?php echo $edit_plan['Amount'] ?? ''; ?>" required><br><br>

        <label>Created By (Admin ID):</label><br>
        <input type="number" name="created_by" value="<?php echo $edit_plan['Created_by'] ?? ''; ?>" required><br><br>

        <?php if ($edit_plan): ?>
            <input type="submit" name="update" value="Update Plan">
        <?php else: ?>
            <input type="submit" value="Add Plan">
        <?php endif; ?>
    </form>

    <h3>All Membership Plans</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Plan ID</th>
            <th>Name</th>
            <th>Duration</th>
            <th>Amount</th>
            <th>Created By</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['Plan_id']; ?></td>
                    <td><?php echo $row['Name']; ?></td>
                    <td><?php echo $row['Duration']; ?></td>
                    <td><?php echo $row['Amount']; ?></td>
                    <td><?php echo $row['Created_by']; ?></td>
                    <td>
                        <a href="?edit=<?php echo $row['Plan_id']; ?>">Edit</a> |
                        <a href="#" onclick="confirmDelete(<?php echo $row['Plan_id']; ?>)">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No plans found.</td></tr>
        <?php endif; ?>
    </table>

    <br>
    <ul>
        <li><a href="../../logout.php">Logout</a></li>
        <li><a href="admin.php">Back to Admin Dashboard</a></li>
    </ul>
</body>
</html>
<?php $conn->close(); ?>

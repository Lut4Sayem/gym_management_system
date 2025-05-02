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
        $sql = "UPDATE membership_plan SET Name='$name', Duration='$duration', Amount='$amount', Created_by='$created_by' WHERE Plan_id='$plan_id'";
    } else {
        $sql = "INSERT INTO membership_plan (Plan_id, Name, Duration, Amount, Created_by) VALUES ('$plan_id', '$name', '$duration', '$amount', '$created_by')";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<p class='message success'>Membership plan saved successfully.</p>";
    } else {
        echo "<p class='message error'>Error: " . $conn->error . "</p>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM membership_plan WHERE Plan_id='$delete_id'");
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
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Page-specific background - keeping this inline to ensure it works */
        body {
            background-image: url('../../images/getimg_ai_img-Xon6oqcqWms7Iv4DJHIfh.jpeg');
            background-size: cover;
            background-position: center;
        }
    </style>
    <script src="../../assets/js/script.js"></script>
</head>
<body>

<div class="container" style="background: rgba(255, 255, 255, 0.95); border-radius: 10px; padding: 30px; margin-top: 60px;">
    <h2>Manage Membership Plans</h2>

    <div class="card">
        <h3><?php echo $edit_plan ? "Update Membership Plan" : "Add New Membership Plan"; ?></h3>
        <form method="POST">
            <div class="form-group">
                <label>Plan ID:</label>
                <input type="number" name="plan_id" class="form-control" 
                       value="<?php echo $edit_plan['Plan_id'] ?? ''; ?>" 
                       required <?php echo $edit_plan ? 'readonly' : ''; ?>>
            </div>

            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" 
                       value="<?php echo $edit_plan['Name'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Duration (Days):</label>
                <input type="number" name="duration" class="form-control" 
                       value="<?php echo $edit_plan['Duration'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Amount:</label>
                <input type="number" step="0.01" name="amount" class="form-control" 
                       value="<?php echo $edit_plan['Amount'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Created By (Admin ID):</label>
                <input type="number" name="created_by" class="form-control" 
                       value="<?php echo $edit_plan['Created_by'] ?? ''; ?>" required>
            </div>

            <div class="form-buttons">
                <?php if ($edit_plan): ?>
                    <button type="submit" name="update" class="btn btn-success">Update Plan</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-success">Add Plan</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>All Membership Plans</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Plan ID</th>
                    <th>Name</th>
                    <th>Duration</th>
                    <th>Amount</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['Plan_id']; ?></td>
                            <td><?php echo $row['Name']; ?></td>
                            <td><?php echo $row['Duration']; ?> days</td>
                            <td>৳<?php echo number_format($row['Amount'], 2); ?></td>
                            <td><?php echo $row['Created_by']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['Plan_id']; ?>" class="btn btn-primary">Edit</a>
                                <a href="#" onclick="confirmDelete(<?php echo $row['Plan_id']; ?>, 'plan')" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No plans found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="navigation">
        <a href="admin.php" class="btn btn-primary">Back to Admin Dashboard</a>
        <a href="../../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>
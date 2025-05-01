<?php
// ------------------------
// DB Connection
// ------------------------
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "GYM_MANAGEMENT_SYSTEM";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ------------------------
// Delete Equipment
// ------------------------
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM gym_equipment WHERE Equipment_ID = $delete_id");
    header("Location: manage_equipment.php");
    exit;
}

// ------------------------
// Update Equipment
// ------------------------
if (isset($_POST['update'])) {
    $equipment_id = $_POST['equipment_id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $quantity = $_POST['quantity'];
    $condition = $_POST['condition'];
    $description = $_POST['description'];
    $admin_id = $_POST['admin_id'];

    $sql = "UPDATE gym_equipment SET 
                Name='$name', 
                Type='$type', 
                Quantity='$quantity', 
                `Condition`='$condition', 
                Description='$description', 
                Admin_id='$admin_id'
            WHERE Equipment_ID='$equipment_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Equipment updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
}

// ------------------------
// Insert New Equipment
// ------------------------
if (isset($_POST['add'])) {
    $equipment_id = $_POST['equipment_id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $quantity = $_POST['quantity'];
    $condition = $_POST['condition'];
    $description = $_POST['description'];
    $admin_id = $_POST['admin_id'];

    $sql = "INSERT INTO gym_equipment (Equipment_ID, Name, Type, Quantity, `Condition`, Description, Admin_id)
            VALUES ('$equipment_id', '$name', '$type', '$quantity', '$condition', '$description', '$admin_id')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>New equipment added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
}

// ------------------------
// Fetch Single Row for Editing
// ------------------------
$edit_row = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM gym_equipment WHERE Equipment_ID = $edit_id");
    $edit_row = $edit_result->fetch_assoc();
}

// ------------------------
// Fetch All Equipment
// ------------------------
$equipment_result = $conn->query("SELECT * FROM gym_equipment");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Gym Equipment</title>
</head>
<body>

    <h2>Manage Gym Equipment</h2>

    <!-- ------------------------ -->
    <!-- Form: Add or Edit Equipment -->
    <!-- ------------------------ -->
    <h3><?php echo isset($edit_row) ? "Edit Equipment" : "Add New Equipment"; ?></h3>
    <form method="POST" action="">
        <label>Equipment ID:</label><br>
        <input type="number" name="equipment_id" required 
               value="<?php echo $edit_row['Equipment_ID'] ?? ''; ?>" 
               <?php echo isset($edit_row) ? 'readonly' : ''; ?>><br><br>

        <label>Name:</label><br>
        <input type="text" name="name" required value="<?php echo $edit_row['Name'] ?? ''; ?>"><br><br>

        <label>Type:</label><br>
        <input type="text" name="type" required value="<?php echo $edit_row['Type'] ?? ''; ?>"><br><br>

        <label>Quantity:</label><br>
        <input type="number" name="quantity" required value="<?php echo $edit_row['Quantity'] ?? ''; ?>"><br><br>

        <label>Condition:</label><br>
        <input type="text" name="condition" required value="<?php echo $edit_row['Condition'] ?? ''; ?>"><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="4" cols="30" required><?php echo $edit_row['Description'] ?? ''; ?></textarea><br><br>

        <label>Admin ID:</label><br>
        <input type="number" name="admin_id" required value="<?php echo $edit_row['Admin_id'] ?? ''; ?>"><br><br>

        <input type="submit" name="<?php echo isset($edit_row) ? 'update' : 'add'; ?>" 
               value="<?php echo isset($edit_row) ? 'Update Equipment' : 'Add Equipment'; ?>">
    </form>

    <!-- ------------------------ -->
    <!-- Equipment Table -->
    <!-- ------------------------ -->
    <h3>All Equipment</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Equipment ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Quantity</th>
            <th>Condition</th>
            <th>Description</th>
            <th>Admin ID</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($equipment_result->num_rows > 0) {
            while ($row = $equipment_result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['Equipment_ID']}</td>
                        <td>{$row['Name']}</td>
                        <td>{$row['Type']}</td>
                        <td>{$row['Quantity']}</td>
                        <td>{$row['Condition']}</td>
                        <td>{$row['Description']}</td>
                        <td>{$row['Admin_id']}</td>
                        <td>
                            <a href='?edit={$row['Equipment_ID']}'>Edit</a> | 
                            <a href='?delete={$row['Equipment_ID']}' onclick='return confirm(\"Are you sure?\");'>Delete</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No equipment found.</td></tr>";
        }

        $conn->close();
        ?>
    </table>

    <br>
    <ul>
        <li><a href="../../logout.php">Logout</a></li>
        <li><a href="admin.php">Back to Admin Dashboard</a></li>
    </ul>

</body>
</html>

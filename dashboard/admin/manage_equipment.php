<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');

// Delete Equipment
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM gym_equipment WHERE Equipment_ID = $delete_id");
    header("Location: manage_equipment.php");
    exit;
}

// Update Equipment
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
        echo "<script>alert('Equipment updated successfully!'); window.location='manage_equipment.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Insert New Equipment
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
        echo "<script>alert('New equipment added successfully!'); window.location='manage_equipment.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Fetch row for editing
$edit_row = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM gym_equipment WHERE Equipment_ID = $edit_id");
    $edit_row = $edit_result->fetch_assoc();
}

// Fetch all equipment
$equipment_result = $conn->query("SELECT * FROM gym_equipment");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Gym Equipment</title>
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
    <h2>Manage Gym Equipment</h2>

    <div class="card">
        <h3><?php echo isset($edit_row) ? "Edit Equipment" : "Add New Equipment"; ?></h3>
        <form method="POST">
            <div class="form-group">
                <label>Equipment ID:</label>
                <input type="number" name="equipment_id" class="form-control" required 
                       value="<?php echo $edit_row['Equipment_ID'] ?? ''; ?>" 
                       <?php echo isset($edit_row) ? 'readonly' : ''; ?>>
            </div>

            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" required value="<?php echo $edit_row['Name'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label>Type:</label>
                <input type="text" name="type" class="form-control" required value="<?php echo $edit_row['Type'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label>Quantity:</label>
                <input type="number" name="quantity" class="form-control" required value="<?php echo $edit_row['Quantity'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label>Condition:</label>
                <input type="text" name="condition" class="form-control" required value="<?php echo $edit_row['Condition'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="4" class="form-control" required><?php echo $edit_row['Description'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label>Admin ID:</label>
                <input type="number" name="admin_id" class="form-control" required value="<?php echo $edit_row['Admin_id'] ?? ''; ?>">
            </div>

            <div class="form-buttons">
                <input type="submit" class="btn btn-success" name="<?php echo isset($edit_row) ? 'update' : 'add'; ?>" 
                       value="<?php echo isset($edit_row) ? 'Update Equipment' : 'Add Equipment'; ?>">
            </div>
        </form>
    </div>

    <div class="card">
        <h3>All Equipment</h3>
        <table class="table">
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
                                <a href='?edit={$row['Equipment_ID']}' class='btn btn-primary'>Edit</a>
                                <a href='?delete={$row['Equipment_ID']}' onclick='return confirmDelete(\"equipment\");' class='btn btn-danger'>Delete</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No equipment found.</td></tr>";
            }
            ?>
        </table>
    </div>

    <div class="navigation">
        <a href="admin.php" class="btn btn-primary">Back to Admin Dashboard</a>
        <a href="../../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>
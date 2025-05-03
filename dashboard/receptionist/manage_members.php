<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');

$user_id = $_SESSION['user_id'] ?? null;

// Fetch all non-admin users along with their Member data
$sql = "
    SELECT u.ID, u.Name, u.Email, u.Age, u.Gender, u.Address, u.User_type, m.Date_of_joining
    FROM User u
    LEFT JOIN Member m ON u.ID = m.Member_ID
    WHERE u.User_type != 'Admin'
";
$result = $conn->query($sql);

// Handle update or delete requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle update
    if (isset($_POST['update_id'])) {
        $update_id = $_POST['update_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];

        $stmt = $conn->prepare("UPDATE User SET Name=?, Email=?, Age=?, Gender=?, Address=? WHERE ID=?");
        $stmt->bind_param("ssissi", $name, $email, $age, $gender, $address, $update_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'User updated successfully.';
        } else {
            $_SESSION['error_message'] = 'Error updating user: ' . $conn->error;
        }
        $stmt->close();
        header("Location: manage_members.php");
        exit();
    }

    // Handle delete
    if (isset($_POST['delete_id'])) {
        $delete_id = (int) $_POST['delete_id'];
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Delete from dependent tables
            $tables = ['Member', 'Trainer', 'Receptionist', 'Admin'];
            foreach ($tables as $table) {
                $field = $table . '_ID';
                $stmt = $conn->prepare("DELETE FROM $table WHERE $field = ?");
                $stmt->bind_param("i", $delete_id);
                $stmt->execute();
                $stmt->close();
            }
            
            // Delete from User
            $stmt = $conn->prepare("DELETE FROM User WHERE ID = ?");
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $stmt->close();
            
            // Commit transaction
            $conn->commit();
            $_SESSION['success_message'] = 'User deleted successfully.';
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $_SESSION['error_message'] = 'Error deleting user: ' . $e->getMessage();
        }
        
        header("Location: manage_members.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Members</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        td, th {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }
        .readonly {
            background-color: #f0f0f0;
            border: none;
        }
        .action-btns button {
            margin-right: 5px;
        }
        .success {
            color: green;
            margin: 10px 0;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
    </style>
    <script>
        function enableEdit(rowId) {
            document.querySelectorAll(`#row_${rowId} input, #row_${rowId} textarea, #row_${rowId} select`).forEach(field => {
                field.removeAttribute('readonly');
                field.removeAttribute('disabled');
                field.classList.remove('readonly');
            });
            document.getElementById(`editBtn_${rowId}`).style.display = 'none';
            document.getElementById(`saveBtn_${rowId}`).style.display = 'inline';
            document.getElementById(`deleteBtn_${rowId}`).style.display = 'none';
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete this user? This action cannot be undone.");
        }

        function confirmUpdate() {
            return confirm("Confirm updating this user's information?");
        }
    </script>
</head>
<body>

<h2>Manage Users (Non-Admins)</h2>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Address</th>
        <th>User Type</th>
        <th>Date of Joining</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr id="row_<?php echo $row['ID']; ?>">
            <form method="POST" onsubmit="return confirmUpdate();">
                <td><input type="text" name="name" value="<?php echo htmlspecialchars($row['Name']); ?>" readonly class="readonly"></td>
                <td><input type="email" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>" readonly class="readonly" required></td>
                <td><input type="number" name="age" value="<?php echo htmlspecialchars($row['Age']); ?>" readonly class="readonly" required></td>
                <td>
                    <select name="gender" disabled class="readonly">
                        <option value="Male" <?php if ($row['Gender'] === 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($row['Gender'] === 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if ($row['Gender'] === 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </td>
                <td><textarea name="address" rows="2" cols="20" readonly class="readonly"><?php echo htmlspecialchars($row['Address']); ?></textarea></td>
                <td><?php echo htmlspecialchars($row['User_type']); ?></td>
                <td><?php echo $row['Date_of_joining'] ?? 'N/A'; ?></td>
                <td class="action-btns">
                    <input type="hidden" name="update_id" value="<?php echo $row['ID']; ?>">
                    <button type="button" id="editBtn_<?php echo $row['ID']; ?>" onclick="enableEdit(<?php echo $row['ID']; ?>)">Edit</button>
                    <button type="submit" id="saveBtn_<?php echo $row['ID']; ?>" style="display:none;">Save</button>
                </td>
            </form>
            <td>
                <form method="POST" onsubmit="return confirmDelete();">
                    <input type="hidden" name="delete_id" value="<?php echo $row['ID']; ?>">
                    <button type="submit" id="deleteBtn_<?php echo $row['ID']; ?>">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="receptionist.php">← Back to Receptionist Dashboard</a> |
<a href="/GYM_MANAGEMENT_SYSTEM/logout.php">Logout</a>

</body>
</html>

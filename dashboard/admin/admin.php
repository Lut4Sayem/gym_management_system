<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');

$user_id = $_SESSION['user_id'] ?? null;

// Fetch admin user info
$sql = "SELECT * FROM User WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];

    $update_sql = "UPDATE User SET Name=?, Email=?, Age=?, Gender=?, Address=? WHERE ID=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssissi", $name, $email, $age, $gender, $address, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Profile updated successfully!'); window.location='admin.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Page-specific background - keeping this inline to ensure it works */
        body {
            background-image: url('../../images/getimg_ai_img-Xon6oqcqWms7Iv4DJHIfh.jpeg');
            background-size: cover;
            background-position: center;
        }
        /* Added spacing for admin function buttons */
        .dashboard-links {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }
        .dashboard-links a {
            min-width: 200px;
            text-align: center;
        }
    </style>
    <script src="../../assets/js/script.js"></script>
</head>
<body>

<div class="container" style="background: rgba(255, 255, 255, 0.95); border-radius: 10px; padding: 30px; margin-top: 60px;">
    <h2>Welcome Admin!</h2>
    
    <div class="card">
        <h3>Your Profile Information</h3>
        <form method="POST">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($admin['Name']); ?>" readonly required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['Email']); ?>" readonly required>
            </div>
            
            <div class="form-group">
                <label>Age:</label>
                <input type="number" name="age" class="form-control" value="<?= htmlspecialchars($admin['Age']); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Gender:</label>
                <select name="gender" class="form-control" disabled required>
                    <option value="Male" <?= $admin['Gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?= $admin['Gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?= $admin['Gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" rows="3" class="form-control" readonly><?= htmlspecialchars($admin['Address']); ?></textarea>
            </div>

            <div class="form-buttons">
                <button type="button" class="btn btn-primary" id="editBtn" onclick="enableEditForm()">Edit</button>
                <button type="submit" name="save" class="btn btn-success" id="saveBtn" style="display: none;">Save</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>Admin Functions</h3>
        <div class="dashboard-links">
            <a href="manage_equipment.php" class="btn btn-primary">Manage Equipment</a>
            <a href="manage_membership_plan.php" class="btn btn-primary">Manage Plans</a>
            <a href="../../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<script>
    // Specific function for this page
    function enableEditForm() {
        const fields = document.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            if (field.name !== 'save') {
                field.removeAttribute('readonly');
                field.removeAttribute('disabled');
            }
        });
        document.getElementById('editBtn').style.display = 'none';
        document.getElementById('saveBtn').style.display = 'inline-block';
    }
</script>

</body>
</html>
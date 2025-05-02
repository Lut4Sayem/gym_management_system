<?php
// --------------------------------------------
// Session & DB Initialization
// --------------------------------------------
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');

// Get logged-in user's ID from session
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: /GYM_MANAGEMENT_SYSTEM/login.php");
    exit();
}

// --------------------------------------------
// Fetch member (user) information
// --------------------------------------------
$user_stmt = $conn->prepare("SELECT * FROM User WHERE ID = ? AND User_type = 'Member'");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

if (!$user) {
    echo "Member not found.";
    exit();
}

// --------------------------------------------
// Handle profile update (email, address, age, password)
// --------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $email = $_POST['email'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $password = $_POST['password']; // not hashed as per instruction

    $update_stmt = $conn->prepare("UPDATE User SET Email = ?, Address = ?, Age = ?, Password = ? WHERE ID = ?");
    $update_stmt->bind_param("ssisi", $email, $address, $age, $password, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    echo "<script>alert('Information updated successfully.'); window.location='member.php';</script>";
    exit();
}

// --------------------------------------------
// Fetch membership plan info
// --------------------------------------------
$plan_stmt = $conn->prepare("
    SELECT mp.Name, mp.Duration, mp.Amount
    FROM membership_plan mp
    JOIN uses u ON mp.Plan_id = u.Plan_ID
    WHERE u.Member_ID = ?
");
$plan_stmt->bind_param("i", $user_id);
$plan_stmt->execute();
$plan_result = $plan_stmt->get_result();
$plan = $plan_result->fetch_assoc();
$plan_stmt->close();

// --------------------------------------------
// Fetch body measurements for the logged-in member
// --------------------------------------------
$bm_stmt = $conn->prepare("
    SELECT Measurement_ID, Height, Weight, Date_of_measurement
    FROM body_measurement
    WHERE Member_ID = ?
    ORDER BY Date_of_measurement DESC
");
$bm_stmt->bind_param("i", $user_id);
$bm_stmt->execute();
$bm_result = $bm_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Dashboard | Gym Management System</title>
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
    <h2>Welcome, <?php echo htmlspecialchars($user['Name']); ?>!</h2>

    <!-- Membership Plan Info -->
    <div class="card">
        <h3>Your Membership Plan</h3>
        <?php if ($plan): ?>
            <table class="table">
                <tr><th>Plan Name</th><td><?php echo $plan['Name']; ?></td></tr>
                <tr><th>Duration</th><td><?php echo $plan['Duration']; ?> days</td></tr>
                <tr><th>Amount</th><td>৳<?php echo $plan['Amount']; ?></td></tr>
            </table>
        <?php else: ?>
            <p>You are not assigned a membership plan.</p>
        <?php endif; ?>
    </div>

    <!-- Member Info Form -->
    <div class="card">
        <h3>Your Information</h3>
        <form method="POST" onsubmit="return confirmUpdate('profile');">
            <table class="table">
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['Email']); ?>" readonly required></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><textarea name="address" rows="3" class="form-control" readonly required><?php echo htmlspecialchars($user['Address']); ?></textarea></td>
                </tr>
                <tr>
                    <th>Age</th>
                    <td><input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($user['Age']); ?>" readonly required></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><input type="text" name="password" class="form-control" value="<?php echo htmlspecialchars($user['Password']); ?>" readonly required></td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td><input type="text" name="gender" class="form-control" value="<?php echo htmlspecialchars($user['Gender']); ?>" readonly></td>
                </tr>
            </table>

            <div class="action-buttons">
                <button type="button" id="editBtn" class="btn btn-primary" onclick="enableEditRow('profile')">Edit</button>
                <button type="submit" name="update" id="updateBtn" class="btn btn-success" style="display: none;">Update</button>
            </div>
        </form>
    </div>

    <!-- Body Measurements Table -->
    <div class="card">
        <h3>Your Body Measurements</h3>
        <?php if ($bm_result->num_rows > 0): ?>
            <table class="table">
                <tr>
                    <th>Height (cm)</th>
                    <th>Weight (kg)</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($bm = $bm_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $bm['Height']; ?></td>
                        <td><?php echo $bm['Weight']; ?></td>
                        <td><?php echo $bm['Date_of_measurement']; ?></td>
                        <td>
                            <button type="button" class="btn btn-primary" onclick="confirmDelete(<?php echo $bm['Measurement_ID']; ?>, 'measurement')">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No body measurements recorded.</p>
        <?php endif; ?>
    </div>

    <!-- Navigation -->
    <div class="navigation">
        <a href="../../logout.php" class="btn btn-primary">Logout</a>
    </div>
</div>

<script>
    // Specific functions for this page
    function enableEditRow(rowId) {
        const fields = ['email', 'address', 'age', 'password'];
        fields.forEach(name => {
            const field = document.querySelector(`[name="${name}"]`);
            if (field) {
                field.removeAttribute('readonly');
                field.classList.remove('readonly');
            }
        });
        document.getElementById('editBtn').style.display = 'none';
        document.getElementById('updateBtn').style.display = 'inline';
    }
</script>

</body>
</html>
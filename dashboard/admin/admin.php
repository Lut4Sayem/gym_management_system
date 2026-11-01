<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');
require_role('Admin');

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age = (int) ($_POST['age'] ?? 0);
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a name and a valid email.';
    } elseif (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
        $error = 'Please pick a gender.';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE user SET Name=?, Email=?, Age=?, Gender=?, Address=? WHERE ID=?");
            $stmt->bind_param("ssissi", $name, $email, $age, $gender, $address, $user_id);
            $stmt->execute();
            $stmt->close();
            $_SESSION['success_message'] = 'Profile updated.';
            header('Location: admin.php');
            exit();
        } catch (mysqli_sql_exception $ex) {
            $error = $ex->getCode() === 1062 ? 'That email is already used by another account.' : 'Could not update profile.';
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM user WHERE ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Quick numbers for the dashboard.
$counts = $conn->query("
    SELECT
        (SELECT COUNT(*) FROM member) AS members,
        (SELECT COUNT(*) FROM trainer) AS trainers,
        (SELECT COUNT(*) FROM gym_equipment) AS equipment,
        (SELECT COALESCE(SUM(Amount_paid), 0) FROM receipt) AS revenue
")->fetch_assoc();

$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/script.js"></script>
</head>
<body class="page-with-bg">

<div class="container">
    <h2>Welcome, <?= e($admin['Name']) ?>!</h2>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
        <h3>At a glance</h3>
        <div class="table-wrap">
            <table class="table">
                <tr><th>Members</th><th>Trainers</th><th>Equipment items</th><th>Total revenue</th></tr>
                <tr>
                    <td><?= e($counts['members']) ?></td>
                    <td><?= e($counts['trainers']) ?></td>
                    <td><?= e($counts['equipment']) ?></td>
                    <td>৳<?= number_format((float) $counts['revenue'], 2) ?></td>
                </tr>
            </table>
        </div>
        <div class="dashboard-links">
            <a href="manage_equipment.php" class="btn btn-primary">Manage Equipment</a>
            <a href="manage_membership_plan.php" class="btn btn-primary">Manage Membership Plans</a>
            <a href="../../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <div class="card">
        <h3>Your Profile</h3>
        <button type="button" class="btn btn-sm btn-secondary" onclick="toggleVisibility('profileDetails', this)">
            <?= $error ? 'Hide Details' : 'Show Details' ?>
        </button>

        <div id="profileDetails" style="display: <?= $error ? 'block' : 'none' ?>; margin-top: 15px;">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" class="form-control" value="<?= e($admin['Name']) ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" value="<?= e($admin['Email']) ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label>Age:</label>
                        <input type="number" name="age" min="0" max="120" class="form-control" value="<?= e($admin['Age']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Gender:</label>
                        <select name="gender" class="form-control" disabled required>
                            <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                                <option value="<?= $g ?>" <?= $admin['Gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <textarea name="address" rows="2" class="form-control" readonly><?= e($admin['Address']) ?></textarea>
                </div>
                <button type="button" class="btn btn-sm btn-primary" data-role="edit" onclick="enableEdit('profileDetails')">Edit Profile</button>
                <button type="submit" name="save" class="btn btn-sm btn-success" data-role="save" style="display: none;">Save Changes</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>

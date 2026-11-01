<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');
require_role('Member');

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $age = (int) ($_POST['age'] ?? 0);
    $new_password = $_POST['new_password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email.';
    } elseif ($age < 12 || $age > 120) {
        $error = 'Age must be between 12 and 120.';
    } elseif ($new_password !== '' && strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters.';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE user SET Email = ?, Address = ?, Age = ? WHERE ID = ?");
            $stmt->bind_param("ssii", $email, $address, $age, $user_id);
            $stmt->execute();

            if ($new_password !== '') {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE user SET Password = ? WHERE ID = ?");
                $stmt->bind_param("si", $hash, $user_id);
                $stmt->execute();
            }
            $_SESSION['success_message'] = 'Your information was updated.';
            header('Location: member.php');
            exit();
        } catch (mysqli_sql_exception $ex) {
            $error = $ex->getCode() === 1062 ? 'That email is already used by another account.' : 'Could not update your information.';
        }
    }
}

$stmt = $conn->prepare("SELECT u.*, m.Date_of_joining FROM user u JOIN member m ON m.Member_ID = u.ID WHERE u.ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
    exit('Member record not found. Please contact the front desk.');
}

// Current plan + when it runs out (last payment for that plan + its duration).
$stmt = $conn->prepare("
    SELECT mp.Name, mp.Duration, mp.Amount,
           MAX(r.Date_of_purchase) AS Last_paid,
           DATE_ADD(MAX(r.Date_of_purchase), INTERVAL mp.Duration DAY) AS Valid_until
    FROM uses u
    JOIN membership_plan mp ON mp.Plan_id = u.Plan_ID
    LEFT JOIN receipt r ON r.Member_id = u.Member_ID AND r.Plan_id = u.Plan_ID
    WHERE u.Member_ID = ?
    GROUP BY mp.Plan_id, mp.Name, mp.Duration, mp.Amount
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$plan = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("
    SELECT u.Name, t.Shift_start, t.Shift_end
    FROM trains tr JOIN trainer t ON t.Trainer_ID = tr.Trainer_ID JOIN user u ON u.ID = t.Trainer_ID
    WHERE tr.Member_ID = ? LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$trainer = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("SELECT Height, Weight, Date_of_measurement FROM body_measurement WHERE Member_ID = ? ORDER BY Date_of_measurement DESC, Measurement_ID DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$measurements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Member Dashboard | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/script.js"></script>
</head>
<body class="page-with-bg">

<div class="container">
    <h2>Welcome, <?= e($user['Name']) ?>!</h2>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
        <h3>Your Membership</h3>
        <?php if ($plan): ?>
            <div class="table-wrap">
                <table class="table">
                    <tr><th>Plan</th><td><?= e($plan['Name']) ?> (<?= e($plan['Duration']) ?> days, ৳<?= number_format((float) $plan['Amount'], 2) ?>)</td></tr>
                    <tr><th>Last payment</th><td><?= e($plan['Last_paid'] ?? 'No receipt on file') ?></td></tr>
                    <?php if ($plan['Valid_until']): ?>
                        <tr><th>Valid until</th><td>
                            <?= e($plan['Valid_until']) ?>
                            <?php if ($plan['Valid_until'] < date('Y-m-d')): ?><strong style="color:#dc3545;">(expired — please renew at the front desk)</strong><?php endif; ?>
                        </td></tr>
                    <?php endif; ?>
                    <tr><th>Trainer</th><td>
                        <?= $trainer ? e($trainer['Name']) . ' (' . e(substr($trainer['Shift_start'], 0, 5)) . '–' . e(substr($trainer['Shift_end'], 0, 5)) . ')' : 'Not assigned yet' ?>
                    </td></tr>
                    <tr><th>Member since</th><td><?= e($user['Date_of_joining']) ?></td></tr>
                </table>
            </div>
        <?php else: ?>
            <p>You don't have a membership plan yet. Visit the front desk to sign up.</p>
        <?php endif; ?>
    </div>

    <div class="card" id="profile">
        <h3>Your Information</h3>
        <form method="POST" onsubmit="return confirmUpdate('profile')">
            <div class="form-row">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" value="<?= e($user['Email']) ?>" readonly required>
                </div>
                <div class="form-group">
                    <label>Age:</label>
                    <input type="number" name="age" min="12" max="120" class="form-control" value="<?= e($user['Age']) ?>" readonly required>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <input type="text" class="form-control" value="<?= e($user['Gender']) ?>" disabled>
                </div>
                <div class="form-group">
                    <label>New password (leave blank to keep):</label>
                    <input type="password" name="new_password" minlength="6" class="form-control" autocomplete="new-password" readonly>
                </div>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" rows="2" class="form-control" readonly><?= e($user['Address']) ?></textarea>
            </div>
            <button type="button" class="btn btn-primary" data-role="edit" onclick="enableEdit('profile')">Edit</button>
            <button type="submit" name="update" class="btn btn-success" data-role="save" style="display: none;">Save</button>
        </form>
    </div>

    <div class="card">
        <h3>Your Progress</h3>
        <?php if ($measurements): ?>
            <div class="table-wrap">
                <table class="table">
                    <tr><th>Date</th><th>Height (cm)</th><th>Weight (kg)</th><th>BMI</th></tr>
                    <?php foreach ($measurements as $m): ?>
                        <tr>
                            <td><?= e($m['Date_of_measurement']) ?></td>
                            <td><?= e($m['Height']) ?></td>
                            <td><?= e($m['Weight']) ?></td>
                            <td><?= $m['Height'] > 0 ? number_format($m['Weight'] / (($m['Height'] / 100) ** 2), 1) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <p class="muted">Your trainer records these. Ask them if something looks wrong.</p>
        <?php else: ?>
            <p>No measurements recorded yet.</p>
        <?php endif; ?>
    </div>

    <div class="navigation">
        <a href="../../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>

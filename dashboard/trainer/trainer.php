<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');
require_role('Trainer');

$trainer_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT u.Name, u.Email, t.Hourly_wage, t.Shift_start, t.Shift_end
    FROM user u JOIN trainer t ON t.Trainer_ID = u.ID
    WHERE u.ID = ?
");
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$trainer = $stmt->get_result()->fetch_assoc();
if (!$trainer) {
    exit('Trainer record not found. Ask an admin to check the trainer table.');
}

// Assigned members with their latest measurement (if any).
$stmt = $conn->prepare("
    SELECT u.ID, u.Name, u.Age, u.Gender,
           bm.Height, bm.Weight, bm.Date_of_measurement
    FROM trains tr
    JOIN user u ON u.ID = tr.Member_ID
    LEFT JOIN body_measurement bm ON bm.Measurement_ID = (
        SELECT b.Measurement_ID FROM body_measurement b
        WHERE b.Member_ID = tr.Member_ID
        ORDER BY b.Date_of_measurement DESC, b.Measurement_ID DESC
        LIMIT 1
    )
    WHERE tr.Trainer_ID = ?
    ORDER BY u.Name
");
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trainer Dashboard | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="page-with-bg">

<div class="container">
    <h2>Welcome, <?= e($trainer['Name']) ?>!</h2>

    <div class="card">
        <h3>Your Shift</h3>
        <p>
            <?= e(substr($trainer['Shift_start'], 0, 5)) ?> – <?= e(substr($trainer['Shift_end'], 0, 5)) ?>
            · ৳<?= number_format((float) $trainer['Hourly_wage'], 2) ?>/hour
            · <?= count($members) ?> assigned member<?= count($members) === 1 ? '' : 's' ?>
        </p>
    </div>

    <div class="card">
        <h3>Your Members</h3>
        <?php if ($members): ?>
            <div class="table-wrap">
                <table class="table">
                    <tr><th>Name</th><th>Age</th><th>Gender</th><th>Latest Height (cm)</th><th>Latest Weight (kg)</th><th>Last Measured</th><th></th></tr>
                    <?php foreach ($members as $m): ?>
                        <tr>
                            <td><?= e($m['Name']) ?></td>
                            <td><?= e($m['Age']) ?></td>
                            <td><?= e($m['Gender']) ?></td>
                            <td><?= e($m['Height'] ?? '—') ?></td>
                            <td><?= e($m['Weight'] ?? '—') ?></td>
                            <td><?= e($m['Date_of_measurement'] ?? 'Never') ?></td>
                            <td><a href="manage_measurements.php?member_id=<?= e($m['ID']) ?>" class="btn btn-sm btn-primary">Measurements</a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php else: ?>
            <p>No members are assigned to you yet. The front desk assigns members to trainers.</p>
        <?php endif; ?>
    </div>

    <div class="navigation">
        <a href="../../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>

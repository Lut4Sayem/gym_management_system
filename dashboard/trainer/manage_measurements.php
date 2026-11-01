<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');
require_role('Trainer');

$trainer_id = $_SESSION['user_id'];
$member_id = (int) ($_GET['member_id'] ?? 0);

// Trainers may only see members assigned to them.
$stmt = $conn->prepare("
    SELECT u.ID, u.Name, u.Age, u.Gender
    FROM trains tr JOIN user u ON u.ID = tr.Member_ID
    WHERE tr.Trainer_ID = ? AND tr.Member_ID = ?
");
$stmt->bind_param("ii", $trainer_id, $member_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();
if (!$member) {
    header('Location: trainer.php');
    exit();
}

$error = '';
$self = 'manage_measurements.php?member_id=' . $member_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $id = (int) $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM body_measurement WHERE Measurement_ID = ? AND Member_ID = ?");
        $stmt->bind_param("ii", $id, $member_id);
        $stmt->execute();
        $_SESSION['success_message'] = 'Measurement deleted.';
        header('Location: ' . $self);
        exit();
    }

    $height = (float) ($_POST['height'] ?? 0);
    $weight = (float) ($_POST['weight'] ?? 0);
    $date = $_POST['date'] ?? '';

    if ($height < 50 || $height > 272) {
        $error = 'Height should be between 50 and 272 cm.';
    } elseif ($weight < 20 || $weight > 400) {
        $error = 'Weight should be between 20 and 400 kg.';
    } elseif (DateTime::createFromFormat('Y-m-d', $date) === false || $date > date('Y-m-d')) {
        $error = 'Pick a real date that isn\'t in the future.';
    } else {
        $stmt = $conn->prepare("INSERT INTO body_measurement (Member_ID, Trainer_ID, Height, Weight, Date_of_measurement) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iidds", $member_id, $trainer_id, $height, $weight, $date);
        $stmt->execute();
        $_SESSION['success_message'] = 'Measurement added.';
        header('Location: ' . $self);
        exit();
    }
}

$stmt = $conn->prepare("
    SELECT bm.Measurement_ID, bm.Height, bm.Weight, bm.Date_of_measurement, u.Name AS Recorded_by
    FROM body_measurement bm LEFT JOIN user u ON u.ID = bm.Trainer_ID
    WHERE bm.Member_ID = ?
    ORDER BY bm.Date_of_measurement DESC, bm.Measurement_ID DESC
");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Measurements: <?= e($member['Name']) ?> | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/script.js"></script>
</head>
<body class="page-with-bg">

<div class="container">
    <h2><?= e($member['Name']) ?> <span class="muted">(<?= e($member['Age']) ?>, <?= e($member['Gender']) ?>)</span></h2>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
        <h3>Add Measurement</h3>
        <form method="POST" action="<?= e($self) ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Height (cm):</label>
                    <input type="number" step="0.01" min="50" max="272" name="height" class="form-control" required
                           value="<?= e($_POST['height'] ?? ($rows[0]['Height'] ?? '')) ?>">
                </div>
                <div class="form-group">
                    <label>Weight (kg):</label>
                    <input type="number" step="0.01" min="20" max="400" name="weight" class="form-control" required
                           value="<?= e($_POST['weight'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Date:</label>
                    <input type="date" name="date" max="<?= date('Y-m-d') ?>" class="form-control" required
                           value="<?= e($_POST['date'] ?? date('Y-m-d')) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-success">Save Measurement</button>
        </form>
    </div>

    <div class="card">
        <h3>History</h3>
        <?php if ($rows): ?>
            <div class="table-wrap">
                <table class="table">
                    <tr><th>Date</th><th>Height (cm)</th><th>Weight (kg)</th><th>Change</th><th>BMI</th><th>Recorded by</th><th></th></tr>
                    <?php foreach ($rows as $i => $r):
                        $prev = $rows[$i + 1] ?? null; // list is newest first
                        $diff = $prev ? $r['Weight'] - $prev['Weight'] : null; ?>
                        <tr>
                            <td><?= e($r['Date_of_measurement']) ?></td>
                            <td><?= e($r['Height']) ?></td>
                            <td><?= e($r['Weight']) ?></td>
                            <td><?= $diff === null ? '—' : sprintf('%+.1f kg', $diff) ?></td>
                            <td><?= $r['Height'] > 0 ? number_format($r['Weight'] / (($r['Height'] / 100) ** 2), 1) : '—' ?></td>
                            <td><?= e($r['Recorded_by'] ?? '—') ?></td>
                            <td>
                                <form method="POST" action="<?= e($self) ?>" onsubmit="return confirmDelete('measurement')">
                                    <input type="hidden" name="delete_id" value="<?= e($r['Measurement_ID']) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php else: ?>
            <p>No measurements yet. Add the first one above.</p>
        <?php endif; ?>
    </div>

    <div class="navigation">
        <a href="trainer.php" class="btn btn-primary">Back to My Members</a>
        <a href="../../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>

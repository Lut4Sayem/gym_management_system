<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');
require_role('Admin');

$admin_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete_id'])) {
            $id = (int) $_POST['delete_id'];
            $stmt = $conn->prepare("DELETE FROM membership_plan WHERE Plan_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $_SESSION['success_message'] = 'Plan deleted.';
            header('Location: manage_membership_plan.php');
            exit();
        }

        $id = (int) ($_POST['plan_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $duration = (int) ($_POST['duration'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);

        if ($id <= 0 || $name === '') {
            $error = 'Plan ID and name are required.';
        } elseif ($duration <= 0 || $amount <= 0) {
            $error = 'Duration and amount must be more than zero.';
        } elseif (isset($_POST['update'])) {
            $stmt = $conn->prepare("UPDATE membership_plan SET Name=?, Duration=?, Amount=?, Created_by=? WHERE Plan_id=?");
            $stmt->bind_param("sidii", $name, $duration, $amount, $admin_id, $id);
            $stmt->execute();
            $_SESSION['success_message'] = 'Plan updated.';
            header('Location: manage_membership_plan.php');
            exit();
        } else {
            $stmt = $conn->prepare("INSERT INTO membership_plan (Plan_id, Name, Duration, Amount, Created_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isidi", $id, $name, $duration, $amount, $admin_id);
            $stmt->execute();
            $_SESSION['success_message'] = 'Plan added.';
            header('Location: manage_membership_plan.php');
            exit();
        }
    } catch (mysqli_sql_exception $ex) {
        $error = match ($ex->getCode()) {
            1062 => 'A plan with that ID already exists.',
            1451 => 'This plan is in use (members or receipts point to it), so it can\'t be deleted.',
            default => 'Database error: ' . $ex->getMessage(),
        };
    }
}

$edit_plan = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM membership_plan WHERE Plan_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_plan = $stmt->get_result()->fetch_assoc();
}
$form = ($error && !isset($_POST['delete_id'])) ? [
    'Plan_id' => $_POST['plan_id'] ?? '', 'Name' => $_POST['name'] ?? '',
    'Duration' => $_POST['duration'] ?? '', 'Amount' => $_POST['amount'] ?? '',
] : ($edit_plan ?? []);
$editing = $edit_plan !== null || ($error && isset($_POST['update']));

$plans = $conn->query("
    SELECT mp.*, (SELECT COUNT(*) FROM uses u WHERE u.Plan_ID = mp.Plan_id) AS member_count
    FROM membership_plan mp ORDER BY mp.Duration
");

$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Membership Plans | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/script.js"></script>
</head>
<body class="page-with-bg">

<div class="container">
    <h2>Manage Membership Plans</h2>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
        <h3><?= $editing ? 'Update Membership Plan' : 'Add New Membership Plan' ?></h3>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Plan ID:</label>
                    <input type="number" name="plan_id" min="1" class="form-control" required
                           value="<?= e($form['Plan_id'] ?? '') ?>" <?= $editing ? 'readonly' : '' ?>>
                </div>
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" class="form-control" required value="<?= e($form['Name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Duration (days):</label>
                    <input type="number" name="duration" min="1" class="form-control" required value="<?= e($form['Duration'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Amount (৳):</label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required value="<?= e($form['Amount'] ?? '') ?>">
                </div>
            </div>
            <?php if ($editing): ?>
                <button type="submit" name="update" class="btn btn-success">Update Plan</button>
                <a href="manage_membership_plan.php" class="btn btn-secondary">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-success">Add Plan</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>All Membership Plans</h3>
        <div class="table-wrap">
            <table class="table">
                <tr><th>Plan ID</th><th>Name</th><th>Duration</th><th>Amount</th><th>Active members</th><th>Actions</th></tr>
                <?php if ($plans->num_rows === 0): ?>
                    <tr><td colspan="6">No plans found.</td></tr>
                <?php endif; ?>
                <?php while ($row = $plans->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($row['Plan_id']) ?></td>
                        <td><?= e($row['Name']) ?></td>
                        <td><?= e($row['Duration']) ?> days</td>
                        <td>৳<?= number_format((float) $row['Amount'], 2) ?></td>
                        <td><?= e($row['member_count']) ?></td>
                        <td>
                            <a href="?edit=<?= e($row['Plan_id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST" onsubmit="return confirmDelete('plan')">
                                <input type="hidden" name="delete_id" value="<?= e($row['Plan_id']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <div class="navigation">
        <a href="admin.php" class="btn btn-primary">Back to Admin Dashboard</a>
        <a href="../../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>

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
            $stmt = $conn->prepare("DELETE FROM gym_equipment WHERE Equipment_ID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $_SESSION['success_message'] = 'Equipment deleted.';
            header('Location: manage_equipment.php');
            exit();
        }

        $id = (int) ($_POST['equipment_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $quantity = (int) ($_POST['quantity'] ?? -1);
        $condition = trim($_POST['condition'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || $name === '' || $type === '' || $condition === '') {
            $error = 'ID, name, type and condition are required.';
        } elseif ($quantity < 0) {
            $error = 'Quantity can\'t be negative.';
        } elseif (isset($_POST['update'])) {
            $stmt = $conn->prepare("UPDATE gym_equipment SET Name=?, Type=?, Quantity=?, `Condition`=?, Description=?, Admin_id=? WHERE Equipment_ID=?");
            $stmt->bind_param("ssissii", $name, $type, $quantity, $condition, $description, $admin_id, $id);
            $stmt->execute();
            $_SESSION['success_message'] = 'Equipment updated.';
            header('Location: manage_equipment.php');
            exit();
        } else {
            $stmt = $conn->prepare("INSERT INTO gym_equipment (Equipment_ID, Name, Type, Quantity, `Condition`, Description, Admin_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ississi", $id, $name, $type, $quantity, $condition, $description, $admin_id);
            $stmt->execute();
            $_SESSION['success_message'] = 'Equipment added.';
            header('Location: manage_equipment.php');
            exit();
        }
    } catch (mysqli_sql_exception $ex) {
        $error = $ex->getCode() === 1062 ? 'An item with that Equipment ID already exists.' : 'Database error: ' . $ex->getMessage();
    }
}

$edit_row = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM gym_equipment WHERE Equipment_ID = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
}
// Keep what the user typed if the save failed.
$form = $error ? [
    'Equipment_ID' => $_POST['equipment_id'] ?? '', 'Name' => $_POST['name'] ?? '', 'Type' => $_POST['type'] ?? '',
    'Quantity' => $_POST['quantity'] ?? '', 'Condition' => $_POST['condition'] ?? '', 'Description' => $_POST['description'] ?? '',
] : ($edit_row ?? []);
$editing = $edit_row !== null || ($error && isset($_POST['update']));

$equipment = $conn->query("SELECT * FROM gym_equipment ORDER BY Equipment_ID");

$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Equipment | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/script.js"></script>
</head>
<body class="page-with-bg">

<div class="container">
    <h2>Manage Gym Equipment</h2>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
        <h3><?= $editing ? 'Edit Equipment' : 'Add New Equipment' ?></h3>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Equipment ID:</label>
                    <input type="number" name="equipment_id" min="1" class="form-control" required
                           value="<?= e($form['Equipment_ID'] ?? '') ?>" <?= $editing ? 'readonly' : '' ?>>
                </div>
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" class="form-control" required value="<?= e($form['Name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Type:</label>
                    <input type="text" name="type" class="form-control" required value="<?= e($form['Type'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" min="0" class="form-control" required value="<?= e($form['Quantity'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Condition:</label>
                    <input type="text" name="condition" class="form-control" required value="<?= e($form['Condition'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="3" class="form-control"><?= e($form['Description'] ?? '') ?></textarea>
            </div>
            <?php if ($editing): ?>
                <button type="submit" name="update" class="btn btn-success">Update Equipment</button>
                <a href="manage_equipment.php" class="btn btn-secondary">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-success">Add Equipment</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>All Equipment</h3>
        <div class="table-wrap">
            <table class="table">
                <tr>
                    <th>ID</th><th>Name</th><th>Type</th><th>Quantity</th>
                    <th>Condition</th><th>Description</th><th>Last edited by (Admin ID)</th><th>Actions</th>
                </tr>
                <?php if ($equipment->num_rows === 0): ?>
                    <tr><td colspan="8">No equipment found.</td></tr>
                <?php endif; ?>
                <?php while ($row = $equipment->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($row['Equipment_ID']) ?></td>
                        <td><?= e($row['Name']) ?></td>
                        <td><?= e($row['Type']) ?></td>
                        <td><?= e($row['Quantity']) ?></td>
                        <td><?= e($row['Condition']) ?></td>
                        <td><?= e($row['Description']) ?></td>
                        <td><?= e($row['Admin_id']) ?></td>
                        <td>
                            <a href="?edit=<?= e($row['Equipment_ID']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST" onsubmit="return confirmDelete('equipment')">
                                <input type="hidden" name="delete_id" value="<?= e($row['Equipment_ID']) ?>">
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

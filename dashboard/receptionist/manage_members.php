<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');
require_role('Receptionist');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_id'])) {
        $id = (int) $_POST['update_id'];
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $age = (int) ($_POST['age'] ?? 0);
        $gender = $_POST['gender'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $trainer_id = (int) ($_POST['trainer_id'] ?? 0);

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($gender, ['Male', 'Female', 'Other'], true)) {
            $_SESSION['error_message'] = 'Name, a valid email and gender are required.';
        } else {
            $conn->begin_transaction();
            try {
                // User_type check keeps this page from editing staff accounts.
                $stmt = $conn->prepare("UPDATE user SET Name=?, Email=?, Age=?, Gender=?, Address=? WHERE ID=? AND User_type='Member'");
                $stmt->bind_param("ssissi", $name, $email, $age, $gender, $address, $id);
                $stmt->execute();

                $stmt = $conn->prepare("DELETE FROM trains WHERE Member_ID = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                if ($trainer_id > 0) {
                    $stmt = $conn->prepare("INSERT INTO trains (Member_ID, Trainer_ID) VALUES (?, ?)");
                    $stmt->bind_param("ii", $id, $trainer_id);
                    $stmt->execute();
                }
                $conn->commit();
                $_SESSION['success_message'] = 'Member updated.';
            } catch (mysqli_sql_exception $ex) {
                $conn->rollback();
                $_SESSION['error_message'] = $ex->getCode() === 1062 ? 'That email is already used by another account.' : 'Could not update member: ' . $ex->getMessage();
            }
        }
    }

    if (isset($_POST['delete_id'])) {
        $id = (int) $_POST['delete_id'];
        $conn->begin_transaction();
        try {
            foreach (['trains', 'uses', 'body_measurement'] as $table) {
                $stmt = $conn->prepare("DELETE FROM $table WHERE Member_ID = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
            }
            $stmt = $conn->prepare("DELETE FROM member WHERE Member_ID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt = $conn->prepare("DELETE FROM user WHERE ID = ? AND User_type = 'Member'");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $conn->commit();
            $_SESSION['success_message'] = 'Member deleted.';
        } catch (mysqli_sql_exception $ex) {
            $conn->rollback();
            // Receipts are payment records; the foreign key protects them on purpose.
            $_SESSION['error_message'] = $ex->getCode() === 1451
                ? 'This member has payment receipts, so they can\'t be deleted.'
                : 'Could not delete member: ' . $ex->getMessage();
        }
    }

    header("Location: manage_members.php");
    exit();
}

$members = $conn->query("
    SELECT u.ID, u.Name, u.Email, u.Age, u.Gender, u.Address, m.Date_of_joining,
           (SELECT Trainer_ID FROM trains t WHERE t.Member_ID = u.ID LIMIT 1) AS Trainer_ID
    FROM user u
    JOIN member m ON u.ID = m.Member_ID
    WHERE u.User_type = 'Member'
    ORDER BY u.Name
");
$trainers = $conn->query("SELECT t.Trainer_ID, u.Name FROM trainer t JOIN user u ON u.ID = t.Trainer_ID ORDER BY u.Name")->fetch_all(MYSQLI_ASSOC);

$success = $_SESSION['success_message'] ?? '';
$error = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Members | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/script.js"></script>
</head>
<body class="page-with-bg">

<div class="container">
    <h2>Manage Members</h2>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <tr>
                    <th>Name</th><th>Email</th><th>Age</th><th>Gender</th><th>Address</th>
                    <th>Trainer</th><th>Joined</th><th>Actions</th>
                </tr>
                <?php if ($members->num_rows === 0): ?>
                    <tr><td colspan="8">No members yet.</td></tr>
                <?php endif; ?>
                <?php while ($row = $members->fetch_assoc()): $f = 'f_' . $row['ID']; ?>
                    <!-- Inputs use form="..." because a <form> can't wrap table cells. -->
                    <tr id="row_<?= e($row['ID']) ?>">
                        <td><input form="<?= $f ?>" type="text" name="name" class="form-control" value="<?= e($row['Name']) ?>" readonly required></td>
                        <td><input form="<?= $f ?>" type="email" name="email" class="form-control" value="<?= e($row['Email']) ?>" readonly required></td>
                        <td><input form="<?= $f ?>" type="number" name="age" min="0" max="120" class="form-control" value="<?= e($row['Age']) ?>" readonly></td>
                        <td>
                            <select form="<?= $f ?>" name="gender" class="form-control" disabled>
                                <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                                    <option value="<?= $g ?>" <?= $row['Gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><textarea form="<?= $f ?>" name="address" rows="2" class="form-control" readonly><?= e($row['Address']) ?></textarea></td>
                        <td>
                            <select form="<?= $f ?>" name="trainer_id" class="form-control" disabled>
                                <option value="0">-- None --</option>
                                <?php foreach ($trainers as $t): ?>
                                    <option value="<?= e($t['Trainer_ID']) ?>" <?= (int) $row['Trainer_ID'] === (int) $t['Trainer_ID'] ? 'selected' : '' ?>><?= e($t['Name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><?= e($row['Date_of_joining'] ?? 'N/A') ?></td>
                        <td>
                            <form id="<?= $f ?>" method="POST" onsubmit="return confirmUpdate('member')">
                                <input type="hidden" name="update_id" value="<?= e($row['ID']) ?>">
                                <button type="button" class="btn btn-sm btn-primary" data-role="edit" onclick="enableEdit('row_<?= e($row['ID']) ?>')">Edit</button>
                                <button type="submit" class="btn btn-sm btn-success" data-role="save" style="display:none;">Save</button>
                            </form>
                            <form method="POST" onsubmit="return confirmDelete('member')">
                                <input type="hidden" name="delete_id" value="<?= e($row['ID']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <div class="navigation">
        <a href="receptionist.php" class="btn btn-primary">Back to Dashboard</a>
        <a href="register_member.php" class="btn btn-success">Register New Member</a>
        <a href="../../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>

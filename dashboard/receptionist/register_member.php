<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');
require_role('Receptionist');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age = (int) ($_POST['age'] ?? 0);
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $trainer_id = (int) ($_POST['trainer_id'] ?? 0);

    if ($name === '' || $address === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please fill in name, a valid email and address.';
    } elseif ($age < 12 || $age > 120) {
        $error = 'Age must be between 12 and 120.';
    } elseif (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
        $error = 'Please pick a gender.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $conn->begin_transaction();
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (Name, Email, Age, Gender, Address, Password, User_type) VALUES (?, ?, ?, ?, ?, ?, 'Member')");
            $stmt->bind_param("ssisss", $name, $email, $age, $gender, $address, $hash);
            $stmt->execute();
            $member_id = $stmt->insert_id;

            $today = date('Y-m-d');
            $stmt = $conn->prepare("INSERT INTO member (Member_ID, Date_of_joining) VALUES (?, ?)");
            $stmt->bind_param("is", $member_id, $today);
            $stmt->execute();

            if ($trainer_id > 0) {
                $stmt = $conn->prepare("INSERT INTO trains (Member_ID, Trainer_ID) VALUES (?, ?)");
                $stmt->bind_param("ii", $member_id, $trainer_id);
                $stmt->execute();
            }

            $conn->commit();
            $_SESSION['success_message'] = "Member registered. Now generate their first receipt.";
            header("Location: generate_receipts.php?member_id=" . $member_id);
            exit();
        } catch (mysqli_sql_exception $ex) {
            $conn->rollback();
            $error = $ex->getCode() === 1062 ? 'An account with that email already exists.' : 'Could not register member: ' . $ex->getMessage();
        }
    }
}

$trainers = $conn->query("SELECT t.Trainer_ID, u.Name FROM trainer t JOIN user u ON u.ID = t.Trainer_ID ORDER BY u.Name");
$old = fn($key) => e($_POST[$key] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Member | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="page-with-bg">

<div class="container">
    <h2>Register New Member</h2>

    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" class="form-control" value="<?= $old('name') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" value="<?= $old('email') ?>" required>
                </div>
                <div class="form-group">
                    <label>Age:</label>
                    <input type="number" name="age" min="12" max="120" class="form-control" value="<?= $old('age') ?>" required>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender" class="form-control" required>
                        <option value="">-- Select --</option>
                        <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                            <option value="<?= $g ?>" <?= ($_POST['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assign Trainer (optional):</label>
                    <select name="trainer_id" class="form-control">
                        <option value="0">-- None --</option>
                        <?php while ($t = $trainers->fetch_assoc()): ?>
                            <option value="<?= e($t['Trainer_ID']) ?>" <?= (int) ($_POST['trainer_id'] ?? 0) === (int) $t['Trainer_ID'] ? 'selected' : '' ?>>
                                <?= e($t['Name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" minlength="6" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" rows="2" class="form-control" required><?= $old('address') ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Register Member</button>
        </form>
    </div>

    <div class="navigation">
        <a href="receptionist.php" class="btn btn-primary">Back to Dashboard</a>
        <a href="../../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>

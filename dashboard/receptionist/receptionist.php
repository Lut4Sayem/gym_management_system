<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');
require_role('Receptionist');

$stmt = $conn->prepare("
    SELECT u.Name, u.Email, u.Age, u.Gender, u.Address, r.Salary
    FROM user u
    JOIN receptionist r ON u.ID = r.Receptionist_ID
    WHERE u.ID = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$receptionist = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$receptionist) {
    exit('Receptionist record not found. Ask an admin to check the receptionist table.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receptionist Dashboard | Gym Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="page-with-bg">

<div class="container">
    <h2>Welcome, <?= e($receptionist['Name']) ?>!</h2>

    <div class="card">
        <h3>Receptionist Dashboard</h3>
        <div class="dashboard-links">
            <a href="register_member.php" class="btn btn-primary">Register New Member</a>
            <a href="generate_receipts.php" class="btn btn-primary">Generate Receipt</a>
            <a href="manage_members.php" class="btn btn-primary">Manage Members</a>
            <a href="../../logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <div class="card">
        <h3>Your Information</h3>
        <div class="table-wrap">
            <table class="table">
                <tr><th>Name</th><td><?= e($receptionist['Name']) ?></td></tr>
                <tr><th>Email</th><td><?= e($receptionist['Email']) ?></td></tr>
                <tr><th>Age</th><td><?= e($receptionist['Age']) ?></td></tr>
                <tr><th>Gender</th><td><?= e($receptionist['Gender']) ?></td></tr>
                <tr><th>Address</th><td><?= e($receptionist['Address']) ?></td></tr>
                <tr><th>Salary</th><td>৳<?= number_format((float) $receptionist['Salary'], 2) ?></td></tr>
            </table>
        </div>
    </div>
</div>

</body>
</html>

<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');

// Get logged-in user's ID from session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: /GYM_MANAGEMENT_SYSTEM/login.php");
    exit();
}

// Fetch receptionist info
$stmt = $conn->prepare("
    SELECT u.Name, u.Email, u.Age, u.Gender, u.Address, r.Salary
    FROM User u
    JOIN Receptionist r ON u.ID = r.Receptionist_ID
    WHERE u.ID = ? AND u.User_type = 'Receptionist'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$receptionist = $result->fetch_assoc();
$stmt->close();

if (!$receptionist) {
    echo "Receptionist not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receptionist Dashboard | Gym Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2, h3 {
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 60%;
            margin-bottom: 20px;
        }
        td, th {
            border: 1px solid #555;
            padding: 10px;
        }
        .action-buttons {
            margin-top: 20px;
        }
        .action-buttons form {
            display: inline;
        }
        button {
            padding: 10px 20px;
            margin-right: 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Welcome, <?php echo htmlspecialchars($receptionist['Name']); ?>!</h2>

<!-- View-only Receptionist Info -->
<h3>Your Information</h3>
<table>
    <tr><th>Name</th><td><?php echo htmlspecialchars($receptionist['Name']); ?></td></tr>
    <tr><th>Email</th><td><?php echo htmlspecialchars($receptionist['Email']); ?></td></tr>
    <tr><th>Age</th><td><?php echo htmlspecialchars($receptionist['Age']); ?></td></tr>
    <tr><th>Gender</th><td><?php echo htmlspecialchars($receptionist['Gender']); ?></td></tr>
    <tr><th>Address</th><td><?php echo htmlspecialchars($receptionist['Address']); ?></td></tr>
    <tr><th>Salary</th><td>৳<?php echo htmlspecialchars($receptionist['Salary']); ?></td></tr>
</table>

<!-- Action Buttons -->
<div class="action-buttons">
    <form action="generate_receipts.php" method="get">
        <button type="submit">Generate Receipt</button>
    </form>

    <form action="register_member.php" method="get">
        <button type="submit">Register New Member</button>
    </form>

    <form action="manage_members.php" method="get">
        <button type="submit">Manage Members</button>
    </form>
</div>

<!-- Navigation -->
<br><br>
<ul>
    <li><a href="../../logout.php">Logout</a></li>
</ul>

</body>
</html>

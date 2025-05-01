<?php
require_once('../../includes/session_check.php');
require_once('../../includes/db_connect.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $date_of_joining = date('Y-m-d');

    // Insert into User table
    $stmt1 = $conn->prepare("INSERT INTO User (Name, Email, Age, Gender, Address, Password, User_type) VALUES (?, ?, ?, ?, ?, ?, 'Member')");
    $stmt1->bind_param("ssisss", $name, $email, $age, $gender, $address, $password);
    $stmt1->execute();
    $member_id = $stmt1->insert_id; // get the new user ID
    $stmt1->close();

    // Insert into Member table
    $stmt2 = $conn->prepare("INSERT INTO Member (Member_ID, Date_of_joining) VALUES (?, ?)");
    $stmt2->bind_param("is", $member_id, $date_of_joining);
    $stmt2->execute();
    $stmt2->close();

    // Redirect to generate_receipts.php with preselected member ID
    header("Location: generate_receipts.php?member_id=" . $member_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register New Member | Receptionist Panel</title>
    <style>
        form {
            width: 60%;
        }
        label {
            display: block;
            margin-top: 12px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
        }
        .back-link {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Register New Member</h2>

<form method="POST">
    <label>Name:
        <input type="text" name="name" required>
    </label>

    <label>Email:
        <input type="email" name="email" required>
    </label>

    <label>Age:
        <input type="number" name="age" required>
    </label>

    <label>Gender:
        <select name="gender" required>
            <option value="">-- Select Gender --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>
    </label>

    <label>Address:
        <textarea name="address" rows="3" required></textarea>
    </label>

    <label>Password:
        <input type="text" name="password" required>
    </label>

    <button type="submit">Register Member</button>
</form>

<div class="back-link">
    <a href="receptionist.php">← Back to Dashboard</a>
</div>

</body>
</html>

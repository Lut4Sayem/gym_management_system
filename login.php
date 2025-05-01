<?php
session_start();

// Corrected database connection
include('includes/db_connect.php');  // ✅ Correct relative path

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Fetch user based on email
    $query = "SELECT * FROM user WHERE Email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Check password
        if ($user['Password'] === $password) { 
            // Set session variables (match exactly with database column names)
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['User_type'] = $user['User_type']; // ⚡ match case with DB column

            // Redirect to dashboard
            header('Location: dashboard_redirect.php'); // ✅ relative path
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "No user found with this email!";
    }
}
?>

<!-- Simple HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Gym Management System</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>

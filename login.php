<?php
session_start();
include('includes/db_connect.php'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM user WHERE Email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if ($user['Password'] === $password) { 
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['User_type'] = $user['User_type'];
            header('Location: dashboard_redirect.php');
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "No user found with this email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Gym Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Page-specific background - keeping this inline */
        body {
            background-image: url('images/getimg_ai_img-Xon6oqcqWms7Iv4DJHIfh.jpeg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-logo {
            max-width: 150px;
            display: block;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <img src="images/getimg_ai_img-Xon6oqcqWms7Iv4DJHIfh.jpeg" alt="Gym Logo" class="login-logo">
        
        <h2 class="text-center">Login</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
<?php
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
require_once('includes/db_connect.php');
require_once('includes/password.php');

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard_redirect.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT ID, Password, User_type FROM user WHERE Email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_matches($password, $user['Password'])) {
        // Upgrade plain-text seed passwords to a hash on first login.
        if (password_needs_rehash($user['Password'], PASSWORD_DEFAULT)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $up = $conn->prepare("UPDATE user SET Password = ? WHERE ID = ?");
            $up->bind_param("si", $hash, $user['ID']);
            $up->execute();
            $up->close();
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['User_type'] = $user['User_type'];
        header('Location: dashboard_redirect.php');
        exit();
    }

    $error = "Wrong email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Gym Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-with-bg login-page">

<div class="login-container">
    <img src="images/gym-logo.jpeg" alt="Gym logo" class="login-logo">
    <h2 class="text-center">Login</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
</div>

</body>
</html>

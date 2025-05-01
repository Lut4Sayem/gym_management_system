
<?php
session_start();
if (isset($_SESSION['User_type'])) {
    switch (strtolower($_SESSION['User_type'])) { // case-insensitive switch
        case 'admin':
            header('Location: dashboard/admin/admin.php');
            break;
        case 'trainer':
            header('Location: dashboard/trainer/trainer.php');
            break;
        case 'receptionist':
            header('Location: dashboard/receptionist/receptionist.php');
            break;
        case 'member':
            header('Location: dashboard/member/member.php');
            break;
        default:
            // Unknown type, logout
            header('Location: logout.php');
            break;
    }
} else {
    // No session found, go back to login
    header('Location: login.php');
}
exit();
?>

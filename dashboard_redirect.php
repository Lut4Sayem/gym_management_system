<?php
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);

$dashboards = [
    'admin'        => 'dashboard/admin/admin.php',
    'trainer'      => 'dashboard/trainer/trainer.php',
    'receptionist' => 'dashboard/receptionist/receptionist.php',
    'member'       => 'dashboard/member/member.php',
];

if (!isset($_SESSION['User_type'])) {
    header('Location: login.php');
} else {
    header('Location: ' . ($dashboards[strtolower($_SESSION['User_type'])] ?? 'logout.php'));
}
exit();

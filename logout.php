<?php
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
session_unset();
session_destroy();
header('Location: login.php');
exit();

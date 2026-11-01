<?php
$servername = "localhost"; // usually localhost
$username = "root";        // default XAMPP username
$password = "";            // default XAMPP password (empty)
$dbname = "gym_management_system";

// XAMPP's php.ini defaults to Europe/Berlin, which makes "today" wrong for the date checks.
date_default_timezone_set('Asia/Dhaka');

// Make every DB error throw, on every PHP version (PHP 8.0 defaults to silent).
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die("Database connection failed. Check includes/db_connect.php and that MySQL is running.");
}

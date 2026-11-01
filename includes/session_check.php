<?php
// Included only from dashboard/<role>/ pages, so the site root is two levels up.
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);

if (!isset($_SESSION['user_id'], $_SESSION['User_type'])) {
    header('Location: ../../login.php');
    exit();
}

// Stops e.g. a logged-in member from opening admin pages by typing the URL.
function require_role(string $role): void
{
    if ($_SESSION['User_type'] !== $role) {
        header('Location: ../../dashboard_redirect.php');
        exit();
    }
}

// Escape anything printed into HTML.
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

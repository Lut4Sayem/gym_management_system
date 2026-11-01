<?php
// True if $input is the right password for the stored value.
// Accepts proper hashes, plus the plain-text passwords in the seed data
// (login.php re-hashes those on first successful login).
function password_matches(string $input, ?string $stored): bool
{
    $stored = (string) $stored;
    if ($stored === '') {
        return false;
    }
    if (password_get_info($stored)['algo'] !== null) {
        return password_verify($input, $stored);
    }
    return hash_equals($stored, $input);
}

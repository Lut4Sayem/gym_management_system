<?php
// Run: php tests/password_test.php
require __DIR__ . '/../includes/password.php';

// Not assert(): it's switched off in most php.ini files.
function check(bool $ok, string $what): void
{
    if (!$ok) {
        fwrite(STDERR, "FAIL: $what\n");
        exit(1);
    }
}

$hash = password_hash('secret123', PASSWORD_DEFAULT);

check(password_matches('secret123', $hash), 'hashed password should match');
check(!password_matches('wrong', $hash), 'wrong password should not match a hash');
check(!password_matches($hash, $hash), 'typing the hash itself must not log you in');
check(password_matches('admin123', 'admin123'), 'legacy plain-text password should match');
check(!password_matches('admin12', 'admin123'), 'wrong legacy password should not match');
check(!password_matches('', ''), 'empty stored password never matches');
check(!password_matches('x', null), 'null stored password never matches');

echo "All password tests passed\n";

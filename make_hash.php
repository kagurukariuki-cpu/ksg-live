<?php
if (PHP_SAPI !== 'cli') { die("Run from command line only.\n"); }
$password = $argv[1] ?? '';
if (!$password) { die("Usage: php make_hash.php \"password\"\n"); }
echo password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) . "\n";
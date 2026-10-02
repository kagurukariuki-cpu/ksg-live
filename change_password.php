<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store');
if (!isset($_SESSION['email'])) { http_response_code(403); echo '{"error":"not authenticated"}'; exit; }
$email = $_SESSION['email'];
$current = $_POST['current'] ?? '';
$new = $_POST['new'] ?? '';
$confirm = $_POST['confirm'] ?? '';
if (!$current || !$new || !$confirm) { echo '{"error":"missing fields"}'; exit; }
if ($new !== $confirm) { echo '{"error":"passwords do not match"}'; exit; }
if (strlen($new) < 8) { echo '{"error":"Password must be at least 8 characters"}'; exit; }
if (!preg_match('/[A-Za-z]/', $new)) { echo '{"error":"Password must contain a letter"}'; exit; }
if (!preg_match('/[0-9]/', $new)) { echo '{"error":"Password must contain a number"}'; exit; }
if ($new === $current) { echo '{"error":"New must differ from current"}'; exit; }
$usersFile = __DIR__ . '/users.json';
$users = json_decode(file_get_contents($usersFile), true);
if (!isset($users[$email])) { echo '{"error":"user not found"}'; exit; }
if (!password_verify($current, $users[$email]['password_hash'])) { usleep(500000); echo '{"error":"Current password incorrect"}'; exit; }
$users[$email]['password_hash'] = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
$users[$email]['must_change_password'] = false;
$users[$email]['password_changed_at'] = time();
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
$_SESSION['must_change_password'] = false;
echo '{"ok":true}';
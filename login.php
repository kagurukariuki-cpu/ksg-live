<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store');
$usersFile = __DIR__ . '/users.json';
if (!file_exists($usersFile)) { echo '{"error":"server not configured"}'; exit; }
$users = json_decode(file_get_contents($usersFile), true);
if (!is_array($users)) { echo '{"error":"server misconfigured"}'; exit; }
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
if (!$email || !$password) { echo '{"error":"invalid login"}'; exit; }
if (!isset($users[$email])) { usleep(500000); echo '{"error":"invalid login"}'; exit; }
$user = $users[$email];
if (!password_verify($password, $user['password_hash'])) { usleep(500000); echo '{"error":"invalid login"}'; exit; }
session_regenerate_id(true);
setcookie(session_name(), session_id(), ['expires' => time() + 8*3600, 'path' => '/', 'httponly' => true, 'samesite' => 'Strict']);
$_SESSION['email'] = $email;
$_SESSION['role'] = $user['role'];
$_SESSION['classes'] = $user['classes'];
$_SESSION['created'] = time();
$_SESSION['must_change_password'] = !empty($user['must_change_password']);
echo json_encode(['email'=>$email, 'role'=>$user['role'], 'classes'=>$user['classes'], 'mustChangePassword'=>!empty($user['must_change_password'])]);
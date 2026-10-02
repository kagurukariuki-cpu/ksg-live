<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store');
if (!isset($_SESSION['email'], $_SESSION['role'], $_SESSION['classes'])) { echo '{"authenticated":false}'; exit; }
if (time() - ($_SESSION['created'] ?? 0) > 8*3600) { session_destroy(); echo '{"authenticated":false}'; exit; }
echo json_encode(['authenticated'=>true, 'email'=>$_SESSION['email'], 'role'=>$_SESSION['role'], 'classes'=>$_SESSION['classes'], 'mustChangePassword'=>!empty($_SESSION['must_change_password'])]);
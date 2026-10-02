<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store');
if (!isset($_SESSION['email'], $_SESSION['role'], $_SESSION['classes'])) { http_response_code(403); echo '{"error":"not authenticated"}'; exit; }
if (!empty($_SESSION['must_change_password'])) { http_response_code(403); echo '{"error":"must change password first"}'; exit; }
$email = $_SESSION['email'];
$role = $_SESSION['role'];
$allowed = $_SESSION['classes'];
$channel = isset($_GET['channel']) ? preg_replace('/[^a-zA-Z0-9 _-]/', '', $_GET['channel']) : '';
if (!$channel) { echo '{"error":"missing channel"}'; exit; }
if ($role !== 'admin' && !in_array($channel, $allowed, true)) { http_response_code(403); echo '{"error":"not enrolled"}'; exit; }
$file = sys_get_temp_dir() . '/ksg_signal_' . md5($channel) . '.json';
$fp = fopen($file, 'c+'); if (!$fp) { echo '[]'; exit; }
flock($fp, LOCK_EX);
$content = stream_get_contents($fp);
$data = $content ? json_decode($content, true) : null;
if (!is_array($data) || !isset($data['msgs'])) $data = ['msgs'=>[], 'next'=>1];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    $parsed = json_decode($body);
    if (!$parsed) { flock($fp, LOCK_UN); fclose($fp); echo '{"error":"bad json"}'; exit; }
    if ($role === 'viewer' && isset($parsed->type) && in_array($parsed->type, ['offer','frame'], true)) {
        flock($fp, LOCK_UN); fclose($fp); http_response_code(403); echo '{"error":"viewers cannot"}'; exit;
    }
    $parsed->email = $email;
    $id = $data['next']++;
    $data['msgs'][] = ['id'=>$id, 'body'=>$parsed];
    if (count($data['msgs']) > 400) $data['msgs'] = array_slice($data['msgs'], -200);
    ftruncate($fp, 0); rewind($fp); fwrite($fp, json_encode($data));
    flock($fp, LOCK_UN); fclose($fp);
    echo json_encode(['id'=>$id]); exit;
}
$since = isset($_GET['since']) ? intval($_GET['since']) : 0;
$result = [];
foreach ($data['msgs'] as $m) { if ($m['id'] > $since) $result[] = $m; }
if (count($data['msgs']) > 200) { $data['msgs'] = array_slice($data['msgs'], -100); ftruncate($fp, 0); rewind($fp); fwrite($fp, json_encode($data)); }
flock($fp, LOCK_UN); fclose($fp);
echo json_encode($result);
<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

$channel = isset($_GET['channel']) ? preg_replace('/[^a-zA-Z0-9 _-]/', '', $_GET['channel']) : 'default';
if (!$channel) { echo '{"error":"missing channel"}'; exit; }

$file = sys_get_temp_dir() . '/ksg_signal_' . md5($channel) . '.json';
$fp = fopen($file, 'c+');
if (!$fp) { echo '[]'; exit; }
flock($fp, LOCK_EX);

$content = stream_get_contents($fp);
$data = $content ? json_decode($content, true) : null;
if (!is_array($data) || !isset($data['msgs'])) $data = ['msgs' => [], 'next' => 1];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    $parsed = json_decode($body);
    if (!$parsed) { flock($fp, LOCK_UN); fclose($fp); echo '{"error":"bad json"}'; exit; }

    $id = $data['next']++;
    $data['msgs'][] = ['id' => $id, 'body' => $parsed];

    if (count($data['msgs']) > 3000) $data['msgs'] = array_slice($data['msgs'], -1500);

    ftruncate($fp, 0); rewind($fp);
    fwrite($fp, json_encode($data));
    flock($fp, LOCK_UN); fclose($fp);
    echo json_encode(['id' => $id]);
    exit;
}

$since = isset($_GET['since']) ? intval($_GET['since']) : 0;
$result = [];
foreach ($data['msgs'] as $m) {
    if ($m['id'] > $since) $result[] = $m;
}

if (count($data['msgs']) > 1500) {
    $data['msgs'] = array_slice($data['msgs'], -750);
    ftruncate($fp, 0); rewind($fp);
    fwrite($fp, json_encode($data));
}

flock($fp, LOCK_UN); fclose($fp);
echo json_encode($result);
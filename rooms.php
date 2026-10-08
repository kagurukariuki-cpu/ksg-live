<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');

$file = sys_get_temp_dir() . '/ksg_signal_' . md5('_live_rooms') . '.json';
if (!file_exists($file)) { echo '[]'; exit; }

$raw = @file_get_contents($file);
$data = $raw ? json_decode($raw, true) : null;
if (!is_array($data) || !isset($data['msgs'])) { echo '[]'; exit; }

$now = time();
$rooms = [];
foreach ($data['msgs'] as $m) {
    $body = isset($m['body']) ? $m['body'] : null;
    if (!$body || !isset($body->room) || !isset($body->t)) continue;
    if ($now - intval($body->t) > 8) continue;
    if (!in_array($body->room, $rooms)) $rooms[] = $body->room;
}
echo json_encode($rooms);
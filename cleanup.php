<?php
$dir = sys_get_temp_dir();
$files = glob($dir . '/ksg_signal_*.json');
$now = time();
$deleted = 0;
foreach ($files as $f) { if ($now - filemtime($f) > 86400) { @unlink($f); $deleted++; } }
header('Content-Type: application/json');
echo json_encode(['deleted'=>$deleted]);
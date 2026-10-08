<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');
$file = __DIR__ . '/rooms.txt';
if (!file_exists($file)) {
    echo '["Class 1","Class 2","Class 3"]';
    exit;
}
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$rooms = array_values(array_filter(array_map('trim', $lines)));
echo json_encode($rooms);

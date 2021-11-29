<?php
function error($msg) {
    header('Content-type: application/json');
    echo json_encode(['success' => false, 'msg' => $msg]);
    return;
}

$filename = "rooms/211016061720.json";

if (!is_writable($filename)) {
    error("O número da sala não existe.");
    return;
}

$scheme = file_get_contents($filename);

$obj = json_decode($scheme, true);
$obj = json_decode($obj, true);

print_r($obj['end_game']);

<?php
include 'App/Quiuq.php';

use App\Quiuq;

function error($msg) {
    header('Content-type: application/json');
    echo json_encode(['success' => false, 'msg' => $msg]);
    return;
}

if (!isset($_POST['room_id'])) {
    error("O número da sala é obrigatório.");
    return;
}

if (!isset($_POST['index'])) {
    error("O seu index é obrigatório.");
    return;
}

$filename = "rooms/".$_POST['room_id'].".json";
if (!is_writable($filename)) {
    error("O número da sala não existe.");
    return;
}

$scheme = file_get_contents($filename);
$obj = json_decode($scheme, true);

array_push($obj['console'], "O jogandor(a) ".$obj['players'][$_POST['index']]['name']." desistiu do jogo.");

unset($obj['players'][$_POST['index']]);
file_put_contents($filename, json_encode($obj));

header('Content-type: application/json');
echo json_encode(['success' => true]);

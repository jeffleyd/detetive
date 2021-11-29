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
    error("O index é obrigatório.");
    return;
}

$filename = "rooms/".$_POST['room_id'].".json";

if (!is_writable($filename)) {
    error("O número da sala não existe.");
    return;
}

$scheme = file_get_contents($filename);
$obj = json_decode($scheme, true);

if ($obj['end_game'] == 1) {
    $wplayer = '';
    foreach ($obj['players'] as $player) {
        if ($player['winner'])
            $wplayer = $player['name'];
    }
    error("O vencedor foi: ". $wplayer);
    return;
}

header('Content-type: application/json');
echo json_encode([
    'success' => true,
    'room_id' => $_POST['room_id'],
    'my_turn' => $obj['players'][$_POST['index']]['my_turn'],
    'mnmb' => $obj['players'][$_POST['index']]['nmb'],
    'guess' => $obj['players'][$_POST['index']]['guess'],
    'console' => $obj['console']
]);

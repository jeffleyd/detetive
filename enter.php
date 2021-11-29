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

if (!isset($_POST['player'])) {
    error("Os dados do jogador é obrigatório.");
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
    error("Essa sala já está com o jogo encerrado. O vencedor foi: ". $wplayer);
    return;
}

$nplayer = json_decode($_POST['player'], true);

array_push($obj['players'], json_decode($_POST['player'], true));
array_push($obj['console'], "O jogandor(a) ".$nplayer['name']." entrou na sala.");

try {
    $game = new Quiuq($obj["players"]);
} catch(\Exception $e) {
    header('Content-type: application/json');
    echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
    return;
}

file_put_contents($filename, json_encode($obj));

header('Content-type: application/json');
echo json_encode(['success' => true, 'room_id' => $_POST['room_id'], 'index' => (count($obj["players"])-1)]);

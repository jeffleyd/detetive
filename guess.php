<?php
include 'App/Quiuq.php';

use App\Quiuq;

function error($msg, $success = false, $server = false) {
    header('Content-type: application/json');
    echo json_encode(['success' => $success, 'server' => $server, 'msg' => $msg]);
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

if (!isset($_POST['guess'])) {
    error("O guess é obrigatório.");
    return;
}

$filename = "rooms/".$_POST['room_id'].".json";

if (!is_writable($filename)) {
    error("O número da sala não existe.");
    return;
}

$scheme = file_get_contents($filename);
$obj = json_decode($scheme, true);

if (count($obj['players']) < 2) {
    error("Você não pode fazer palpite com apenas 1 jogador na sala.", true, true);
    return;
}

if ($obj['end_game'] == 1) {
    $wplayer = '';
    foreach ($obj['players'] as $player) {
        if ($player['winner'])
            $wplayer = $player['name'];
    }
    error("O vencedor foi: ". $wplayer);
    return;
}

try {
    $game = new Quiuq($obj["players"]);
    $result = $game->guess($_POST['guess'], $_POST['index']);

    if ($result['result'] == false) {
        error($result['msg']);
        return;
    }

    if ($result['end_game'] == true) {
        $obj['end_game'] = 1;
    }

    $obj["players"] = $game->players;

    array_push($obj["players"][$_POST['index']]['guess'], $_POST['guess']);
    array_push($obj['console'], $result['msg']);

    file_put_contents($filename, json_encode($obj));

} catch(\Exception $e) {
    header('Content-type: application/json');
    echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
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

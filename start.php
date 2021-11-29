<?php
include 'App/Quiuq.php';

use App\Quiuq;

if (!isset($_POST['scheme'])) {
    header('Content-type: application/json');
    echo json_encode(['success' => false, 'msg' => "O campo scheme é obrigatório."]);
    return;
}

try {

    $obj = json_decode($_POST['scheme'], true);
    $game = new Quiuq($obj["players"]);

} catch(\Exception $e) {
    header('Content-type: application/json');
    echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
    return;
}

$scheme = $_POST['scheme'];
$scheme = json_decode($scheme, true);

$room_id = date('ymdhis');
$filename = "rooms/".$room_id.".json";
fopen($filename, "w");

$handle = fopen($filename, 'a');
fwrite($handle, json_encode($scheme));

header('Content-type: application/json');
echo json_encode(['success' => true, 'room_id' => $room_id, 'index' => 0]);

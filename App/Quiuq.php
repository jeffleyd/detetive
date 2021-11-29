<?php

namespace App;

class Quiuq {

    public $players;

    function __construct(array $players) {
        $this->players = $players;
        $this->rulesGame();
    }

    public function rulesGame() {
        foreach ($this->players as $indx => $player) {
            $arrNmbs = str_split($player['nmb']);

            foreach ($arrNmbs as $nindex => $n) {

                $cnmb = 0;
                foreach ($arrNmbs as $jexists) {
                    if ($n == $jexists)
                        ++$cnmb;
                    if ($cnmb > 1)
                        throw new \Exception('O jogador(a) '.$player['name'].' está com o número duplicado. '.$player['nmb']);
                }

                if ($nindex > 0) {
                    if (
                        ($n-1) == $arrNmbs[($nindex-1)]
                        or (isset($arrNmbs[($nindex+1)]) and ($n+1) == $arrNmbs[($nindex+1)])
                    ) {
                        throw new \Exception('O jogador(a) '.$player['name'].' está com um número sequêncial. '.$player['nmb']);
                    }
                }

            }
        }
    }


    public function guess($nmb, $pindex) {

        if ($this->players[$pindex]['has_lose']) {
            return [
                'end_game', 0,
                'result' => false,
                'msg' => 'Você está fora do jogo.'
            ];
        }

        $output = '';

        foreach ($this->players as $indx => $player) {

            if ($indx != $pindex and !$player['has_lose']) {

                if ($player['nmb'] == $nmb) {

                    $this->players[$indx]['has_lose'] = 1;
                    $output .= "Jogador(a): ".$player['name'].", perdeu. \n";
                    if ($this->verifyEndGame($pindex)) {
                        $this->players[$pindex]['winner'] = 1;
                        return [
                            'end_game' => true,
                            'result' => true,
                            'msg' => "O jogador(a) ".$this->players[$pindex]['name']." venceu a partida."
                        ];
                    }

                } else {
                    $output .= $player['name']." ".$this->searchTrack($player['nmb'], $nmb)." \n";
                }
            }
        }

        $this->nextTurn($pindex+1, $pindex);

        return [
            'end_game' => false,
            'result' => true,
            'msg' => $output
        ];
    }

    private function nextTurn($i, $pindex) {
        if (isset($this->players[$i]) and $i != $pindex) {
            $this->players[$i]['my_turn'] = 1;
            $this->players[$pindex]['my_turn'] = 0;
        } else {
            if (isset($this->players[($i+1)])) {
                return $this->nextTurn(++$i, $pindex);
            } else {
                return $this->nextTurn(0, $pindex);
            }
        }
    }

    private function verifyEndGame($pindex) {
        foreach ($this->players as $indx => $player) {
            if ($indx != $pindex) {
                if (!$player['has_lose']) {
                    return false;
                }
            }
        }
        return true;
    }

    private function searchTrack($pnmb, $gnmb) {
        $arrPnmb = str_split($pnmb);
        $arrGnmb = str_split($gnmb);

        $a = 0;
        $b = 0;

        foreach($arrGnmb as $index => $arg) {

            if ($arg == $arrPnmb[$index]) {
                ++$a;
            }

            foreach ($arrPnmb as $arp) {
                if ($arg != $arrPnmb[$index] and $arg == $arp) {
                    ++$b;
                }
            }

        }

        $output = $gnmb;
        if ($a)
            $output .= " A".$a." ";
        if ($b)
            $output .= " B".$b." ";

        return $output;

    }

}

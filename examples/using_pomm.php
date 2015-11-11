<?php

list($pomm, $rulerz) = require __DIR__ . '/bootstrap/bootstrap_pomm.php';

$players = $pomm['test_rulerz']->getModel('\Entity\Pomm\TestRulerz\PublicSchema\PlayersModel');
$rule = 'points > 300';

foreach ($rulerz->filter($players, $rule) as $player) {
    var_dump($player->getPseudo());
}

<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use TibiaParser\Player;
use TibiaParser\World;

$player = new Player('Kharsek');
if ($player->exists) {
    var_dump($player);
} else {
    echo 'Character does not exist.';
}

// Get Players online on Antica
$world = new World('Antica');
if ($world->error != NULL) {
	echo $world->error;
} else {
	var_dump($world->information);
	foreach ($world->players as $player) {
		var_dump($player);
	}
}

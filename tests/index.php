<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use TibiaParser\Parser\Tibia;

$tibia = new Tibia();
$player = $tibia->getPlayer('Burchan');
if ($player['exists']) {
	var_dump($player);
} else {
	echo 'Character does not exist.';
}

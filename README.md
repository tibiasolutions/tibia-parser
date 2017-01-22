# Tibia Parser

[![Total Downloads](https://img.shields.io/packagist/dt/tibiasolutions/tibia-parser.svg?style=flat-square)](https://packagist.org/packages/tibiasolutions/tibia-parser)
[![License](https://img.shields.io/packagist/l/tibiasolutions/tibia-parser.svg?style=flat-square)](https://packagist.org/packages/tibiasolutions/tibia-parser)

A PHP crawler module to get tibia.com parsed data.  

## Installation

1. Download [Composer](http://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run
```bash
php composer.phar require tibiasolutions/tibia-parser
```

If Composer is installed globally, run
```bash
composer require tibiasolutions/tibia-parser
```

## Basic Usage

### Player
```php
require dirname(__DIR__) . '/vendor/autoload.php';

use TibiaParser\Player;

$player = new Player('Kharsek');
if ($player->exists) {
	var_dump($player);
} else {
	echo 'Character does not exist.';
}
```

### World
```php
require dirname(__DIR__) . '/vendor/autoload.php';

use TibiaParser\World;

$world = new World('Antica');
if ($world->error != NULL) {
	echo $world->error;
} else {
	var_dump($world->information);
	foreach ($world->players as $player) {
		var_dump($player);
	}
}

// Get all World Names
var_dump(World::getWorlds());
```

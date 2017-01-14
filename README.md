# Tibia Parser
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
```php
require dirname(__DIR__) . '/vendor/autoload.php';

use TibiaParser\Parser\Tibia;

$tibia = new Tibia();
$player = $tibia->getPlayer('Burchan');
if ($player['exists']) {
	var_dump($player);
} else {
	echo 'Character does not exist.';
}

```

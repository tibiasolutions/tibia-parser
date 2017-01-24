<?php
/**
 * Tibia Parser - Tibia.com parser informations
 * Copyright (c) 2017 Tibia Solutions (http://tibia.solutions)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Tibia Solutions (http://tibia.solutions)
 * @link      https://github.com/tibiasolutions/tibiaparser Project
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace TibiaParser;

use Carbon\Carbon;

class Util
{
	/* For some reason, Tibia uses non-breaking spaces (&nbsp;) on dates and
	 * Carbon can't handle those, so we convert them to regular spaces here.
	 */
	static function parseDate($dateString) {
		$dateString = htmlentities($dateString, null, 'utf-8');
		$dateString = str_replace('&nbsp;', ' ', $dateString);

		return Carbon::parse($dateString);
	}
}

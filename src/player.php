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

use TibiaParser\Util;
use Symfony\Component\DomCrawler\Crawler;

class Player
{
    private $player = [ "exists" => true ];

    function __construct($name) {
		$name = str_replace(" ", "+", $name);
		$url = sprintf("https://secure.tibia.com/community/?subtopic=characters&name=%s", $name);
		$html = file_get_contents($url);

		if (stripos($html, "<b>Could not find character</b>") !== false) {
			$this->player["exists"] = false;
		} else {
			$crawler = new Crawler();
			$crawler->addHtmlContent($html);

			$info = $crawler->filterXPath('//div[@class="BoxContent"]/table[1]/tr[position() > 1]');
			foreach ($info as $row) {
				$key  = strtolower(str_replace(" ", "_", str_replace(":", "", trim($row->firstChild->nodeValue))));
				$value = trim($row->lastChild->nodeValue);

				if (strpos($key, "guild") !== false) {
					$this->player["guild"] = $value;
				} else if (strpos($key, "status") !== false) {
					$this->player["account_status"] = $value;
				} else {
					$this->player[$key] = ctype_digit($value) ? (int)$value : $value;
				}
			}

			if (isset($this->player["house"])) {
				$explode = explode(" is paid until ", $this->player["house"]);
				$house_explode = explode(" (", $explode[0]);
				$this->player["house"] = [
					"name"  =>  $house_explode[0],
					"city"  =>  str_replace(")", "", $house_explode[1])
				];
			}

			if (isset($this->player["guild"])) {
				$explode = explode(" of the ", $this->player["guild"]);
				$this->player["guild"] = [
					"rank"  =>  $explode[0],
					"name"  =>  $explode[1]
				];
			}

			if (isset($this->player["last_login"])) {
				$this->player["last_login"] = Util::parseDate($this->player["last_login"]);
			}

			$achievs = $crawler->filterXPath('//b[text()="Account Achievements"]/ancestor::table[1]//tr[position() > 1]');

			$achievements = [];
			if (trim($achievs->text()) !== "There are no achievements set to be displayed for this character.") {
				foreach ($achievs as $row) {
					$stars = new Crawler($row->firstChild);
					$stars = $stars->filterXPath("//img");
					$stars = $stars->count();
					$value = trim($row->lastChild->nodeValue);
					$secret = new Crawler($row->lastChild);
					$secret = $secret->filterXPath("//img");
					$secret = $secret->count() > 0;

					$achievements[] = [
						"stars" => $stars,
						"description" => $value,
						"secret" => $secret
					];
				}
			}

			$this->player["achievements"] = $achievements;

			$death = $crawler->filterXPath('//b[text()="Character Deaths"]/ancestor::table[1]//tr[position() > 1]');
			$deaths = [];
			if ($death->count() > 0) {
				foreach ($death as $row) {
					$date = Util::parseDate(trim($row->firstChild->nodeValue));
					$value = trim($row->lastChild->nodeValue);

					preg_match("/at Level (\\d+) by (.+)\\./", $value, $matches);

					$level = $matches[1];
					$killers = [];
					$assistants = [];

					$assisted = explode(".Assisted by ", $matches[2]);

					if (count($assisted) > 1) {
						$k1 = explode(" and ", $assisted[0]);
						$k = explode(", ", $k1[0]);

						if (count($k1) > 1)
							array_push($k, $k1[1]);

						$killers[] = $k;

						$a1 = explode(" and ", $assisted[1]);
						$a = explode(", ", $a1[0]);

						if (count($a1) > 1)
							array_push($a, $a1[1]);

						$assistants[] = $a;
					} else {
						$k1 = explode(" and ", $matches[2]);
						$k = explode(", ", $k1[0]);

						if (count($k1) > 1)
							array_push($k, $k1[1]);

						$killers[] = $k;
					}

					$deaths[] = [
						"date" => $date,
						"level" => $level,
						"killers" => $killers,
						"assistants" => $assistants
					];
				}
			}

			$this->player["deaths"] = $deaths;

			$ainfo = $crawler->filterXPath('//b[text()="Account Information"]/ancestor::table[1]//tr[position() > 1]');
			$account_information = [];
			if ($ainfo->count() > 0) {
				foreach ($ainfo as $row) {
					$key  = strtolower(str_replace(' ', '_', str_replace(':', '', trim($row->firstChild->nodeValue))));
					$value = trim($row->lastChild->nodeValue);

					$account_information[$key] = $value;
				}
			}

			$this->player["account_information"] = $account_information;
		}
	}

	public function __get($key) {
		return $this->player[$key];
	}

	public function __set($key, $value) {
		$this->player[$key] = $value;
	}

	public function __toString() {
		return json_encode($this->player);
	}
}

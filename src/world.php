<?php
/**
 * Tibia Parser - Tibia.com parser informations
 * Copyright (c) Tibia Solutions (http://tibia.solutions)
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

use Symfony\Component\DomCrawler\Crawler;

class World
{
    private $world = [ "error" => NULL ];
    
    function __construct($world_name) {
        $html = file_get_contents("https://secure.tibia.com/community/?subtopic=worlds&world=" . $world_name);
		if (stripos($html, "World with this name doesn't exist!") !== false) {
			$this->world["error"] = "World with this name doesn't exist!";
		} else {
			$crawler = new Crawler();
			$crawler->addHtmlContent($html);
			
			$information = [ ];
			$info = $crawler->filterXPath('//div[@class="BoxContent"]/div[@class="TableContainer"][1]/table/tr/td/div[@class="InnerTableContainer"]/table/tr');
			foreach ($info as $row) {
				$key  = strtolower(str_replace(" ", "_", str_replace(":", "", trim($row->firstChild->nodeValue))));
				$value = trim($row->lastChild->nodeValue);

				if ($key === "online_record") {
					$record = [];
					$explode = explode(" players (on ", $value);
					$record["players"] = (int)$explode[0];
					$record["date"] = str_replace(")", "", $explode[1]);
			
					$information[$key] = $record;
				} else if ($key === "world_quest_titles") {
					$quests = explode(", ", $value);
					$information[$key] = $quests;
				} else {
					$information[$key] = ctype_digit($value) ? (int)$value : $value;
				}
			}
			
			$this->world["information"] = $information;
			
			$players = [ ];
			$online = $crawler->filterXPath('//div[@class="BoxContent"]/div[@class="TableContainer"][2]/table/tr/td/div[@class="InnerTableContainer"]/table/tr[position() > 1]');
			foreach ($online as $row) {
				$name = $row->childNodes[0]->nodeValue;
				$level = (int)$row->childNodes[1]->nodeValue;
				$vocation = $row->childNodes[2]->nodeValue;
				$players[] = [
					"name" => $name,
					"level" => $level,
					"vocation" => $vocation
				];
			}
			
			$this->world["players"] = $players;
			
		}
    }
    
    public function __get($key) {
        return $this->world[$key];
    }
    
    public function __set($key, $value) {
        $this->world[$key] = $value;
    }
    
    public function __toString() {
        return json_encode($this->player);
    }
}

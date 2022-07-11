<?php

namespace oirancage\strictformexample;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	public function onEnable() : void{
		$this->getServer()->getCommandMap()->registerAll(
			"strictformexample",
			[

			]
		);
	}
}
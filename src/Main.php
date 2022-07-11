<?php

namespace oirancage\strictformexample;

use oirancage\strictformexample\command\StrictFormExampleCommand;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	public function onEnable() : void{
		$this->getServer()->getCommandMap()->register(
			"strictformexample",
			new StrictFormExampleCommand()
		);
	}
}
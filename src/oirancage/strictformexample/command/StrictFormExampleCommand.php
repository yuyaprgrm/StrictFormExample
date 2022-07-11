<?php

namespace oirancage\strictformexample\command;

use oirancage\strictform\component\Button;
use oirancage\strictform\component\Dropdown;
use oirancage\strictform\component\exception\InvalidFormResponseException;
use oirancage\strictform\component\Input;
use oirancage\strictform\component\Slider;
use oirancage\strictform\component\StepSlider;
use oirancage\strictform\component\StringEnumOption;
use oirancage\strictform\component\Toggle;
use oirancage\strictform\CustomForm;
use oirancage\strictform\ModalForm;
use oirancage\strictform\response\CustomFormResponse;
use oirancage\strictform\response\ModalFormResponse;
use oirancage\strictform\response\SimpleFormResponse;
use oirancage\strictform\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\Player;

class StrictFormExampleCommand extends Command{

	public function __construct(){
		parent::__construct("strictformexample", "to show example forms built with StrictForm.", "usage: /strictformexample <form-name>", ["sfe"]);
	}

	/**
	 * @inheritdoc
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$sender instanceof Player){
			$sender->sendMessage("Command is only available in game.");
			return;
		}

		if(count($args) !== 1){
			throw new InvalidCommandSyntaxException;
		}

		switch($args[0]){
			case "simple":
				$form = new SimpleForm(
					"Market",
					"Choose product to buy",
					[
						new Button("apple", "apple $3"),
						new Button("orange", "orange $2"),
						new Button("banana", "banana $5"),
					]
				);
				$form->onClose(function(Player $from) : void{
					$from->sendMessage("You choose nothing and closed form.");
				});
				$form->onSuccess(function(SimpleFormResponse $response) : void{
					$from = $response->getFrom();
					$value = $response->getSelectedButtonValue();
					/**
					 * when you press button with 'apple $3', then value will be 'apple', the name of the button.
					 */
					$from->sendMessage("You choose $value!");
				});
				$form->onValidationError(function(Player $from, InvalidFormResponseException $exception) : void{
					$from->sendMessage("Error: {$exception->getMessage()}");
				});
				break;
			case "custom":
				$form = new CustomForm(
					"Submission",
					[
						new Input("name", "Name", "famima65536"),
						new Slider("age", "Age", 0, 100, 1),
						new Dropdown("gender", "Gender", [
							new StringEnumOption("male", "Male"),
							new StringEnumOption("female", "Female")
						], 0),
						new StepSlider("skill-level", "Programming Skill", [
							new StringEnumOption("beginner", "Beginner"),
							new StringEnumOption("intermediate", "Intermediate"),
							new StringEnumOption("advanced", "Advanced")
						]),
						new Toggle("some-toggle", "toggle")
					]
				);
				$form->onSuccess(function(CustomFormResponse $response) : void{
					$from = $response->getFrom();
					$name   = $response->getInputValue("name");
					$age    = $response->getSliderValue("age");
					$gender = $response->getDropdownValue("gender");
					$level  = $response->getStepSliderValue("skill-level");
					$toggle = $response->getToggleValue("some-toggle");
					$content = <<<content
						name   ${name}
						age    ${age}
						gender ${gender}
						skill  ${level}
						toggle ${toggle}
						content;

					$form = new ModalForm("confirm?", $content, "Yes", "No");
					$form->onClose(function(Player $from) : void{
						$from->sendMessage("You choose nothing and closed form.");
					});
					$form->onSuccess(function(ModalFormResponse $response) : void{
						$from = $response->getFrom();
						$confirmed = $response->getSelectedValue();
						if($confirmed){
							$from->sendMessage("Thank you for submission!");
						}else{
							$from->sendMessage("Submission is aborted.");
						}
					});
					$from->sendForm($form);
				});
				break;
			default:
				$sender->sendMessage("there is no form named {$args[0]}");
				return;
		}
		$sender->sendForm($form);
	}
}
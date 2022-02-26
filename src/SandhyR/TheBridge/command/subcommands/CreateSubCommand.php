<?php

namespace SandhyR\TheBridge\command\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use SandhyR\TheBridge\TheBridge;

class CreateSubCommand extends BaseSubCommand{

    protected function prepare(): void
    {
        $this->registerArgument(0 ,new RawStringArgument("arena"));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
       if(!TheBridge::getInstance()->createArena($args["arena"])){
           $sender->sendMessage("Arena " . $args["arena"] . " Already exist!");
           return;
       }
       $sender->sendMessage("Succesfully create " . $args["arena"] . " Arena");
    }
}
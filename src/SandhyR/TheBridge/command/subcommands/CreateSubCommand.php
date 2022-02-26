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

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
       if(!TheBridge::getInstance()->createArena($args["arena"])){

       }
    }
}
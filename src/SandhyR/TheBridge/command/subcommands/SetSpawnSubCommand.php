<?php

namespace SandhyR\TheBridge\command\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use SandhyR\TheBridge\TheBridge;

class SetSpawnSubCommand extends BaseSubCommand{

    public function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("arena"));
        $this->registerArgument(0, new RawStringArgument("team"));
        $this->setPermission("bedwars.set");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$this->testPermissionSilent($sender)){
            return;
        }
        if(TheBridge::getInstance()->getGame($args["arena"]) == null){
            $sender->sendMessage("Arena " . $args["arena"] . " Not found!");
            return;
        }

        if(!in_array(strtolower($args["team"]), ["blue", "red"])) {
            $sender->sendMessage($args["team"] . " Team not found use red or blue!");
            return;
        }
    }
}

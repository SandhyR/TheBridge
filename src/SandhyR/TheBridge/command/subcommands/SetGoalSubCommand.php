<?php

namespace SandhyR\TheBridge\command\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SandhyR\TheBridge\TheBridge;

class SetGoalSubCommand extends BaseSubCommand{

    public function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("arena", false));
        $this->registerArgument(1, new RawStringArgument("team", false));
        $this->setPermission("thebridge.set");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player){
            return;
        }

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
        TheBridge::getInstance()->getGame($args["arena"])->setGoalPos(strtolower($args["team"]), $sender->getPosition()->asVector3());
        $sender->sendMessage("Succesfully set " .  strtolower($args["team"]) . " goal position");
    }
}

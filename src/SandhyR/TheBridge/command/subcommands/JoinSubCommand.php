<?php

namespace SandhyR\TheBridge\command\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SandhyR\TheBridge\TheBridge;

class JoinSubCommand extends BaseSubCommand{

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("arena", false));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if($sender instanceof Player) {
            if (TheBridge::getInstance()->getGame($args["arena"]) !== null and TheBridge::getInstance()->getGame($args["arena"])->isRunning()) {
                TheBridge::getInstance()->getGame($args["arena"])->addPlayer($sender);
            } else {
                $sender->sendMessage("Arena " . $args["arena"] . " Not found");
            }
        }
    }
}

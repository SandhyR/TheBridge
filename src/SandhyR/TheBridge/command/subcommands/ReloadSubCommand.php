<?php

namespace SandhyR\TheBridge\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use SandhyR\TheBridge\TheBridge;

class ReloadSubCommand extends BaseSubCommand{

    protected function prepare(): void
    {
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
        $count = 0;
        foreach (TheBridge::getInstance()->getGames() as $game){
            $game->reload();
            ++$count;
        }
        $sender->sendMessage("Successfully reload " . $count . " arenas");
    }
}

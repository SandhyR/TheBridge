<?php

namespace SandhyR\TheBridge\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class HelpSubCommand extends BaseSubCommand{
    protected function prepare(): void
    {
        //NOOP
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage("TheBridge §aCommands");
        $sender->sendMessage(TextFormat::GREEN . "/thebridge list " . TextFormat::YELLOW . "Display list of loaded games");
        $sender->sendMessage(TextFormat::GREEN . "/thebridge create " . TextFormat::YELLOW . "Create new game");
        $sender->sendMessage(TextFormat::GREEN . "/thebridge delete " . TextFormat::YELLOW . "Delete existing game");
        $sender->sendMessage(TextFormat::GREEN . "/thebridge quit " . TextFormat::YELLOW . "leave of a game");
        $sender->sendMessage(TextFormat::GREEN . "/thebridge join " . TextFormat::YELLOW . "join of a game");
        $sender->sendMessage(TextFormat::GREEN . "/thebridge setspawn " . TextFormat::YELLOW . "Set position of spawn position a team");
        $sender->sendMessage(TextFormat::GREEN . "/thebridge setgoal " . TextFormat::YELLOW . "Set position of goal a team");
        $sender->sendMessage(TextFormat::GREEN . "/thebridge reload " . TextFormat::YELLOW . "Reload all arena");
    }
}
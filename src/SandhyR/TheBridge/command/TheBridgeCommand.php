<?php

namespace SandhyR\TheBridge\command;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use SandhyR\TheBridge\command\subcommands\CreateSubCommand;
use SandhyR\TheBridge\command\subcommands\HelpSubCommand;

class TheBridgeCommand extends BaseCommand{

    protected function prepare(): void
    {
        $this->registerSubCommand(new HelpSubCommand("help"));
        $this->registerSubCommand(new CreateSubCommand("create"));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $this->sendUsage();
    }
}
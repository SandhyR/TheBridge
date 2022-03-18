<?php

namespace SandhyR\TheBridge\command;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use SandhyR\TheBridge\command\subcommands\CreateSubCommand;
use SandhyR\TheBridge\command\subcommands\HelpSubCommand;
use SandhyR\TheBridge\command\subcommands\JoinSubCommand;
use SandhyR\TheBridge\command\subcommands\QuitSubCommand;
use SandhyR\TheBridge\command\subcommands\RandomJoinSubCommand;
use SandhyR\TheBridge\command\subcommands\ReloadSubCommand;
use SandhyR\TheBridge\command\subcommands\SetGoalSubCommand;
use SandhyR\TheBridge\command\subcommands\SetHubSubCommand;
use SandhyR\TheBridge\command\subcommands\SetSpawnSubCommand;
use SandhyR\TheBridge\command\subcommands\SetWorldSubCommand;

class TheBridgeCommand extends BaseCommand{

    protected function prepare(): void
    {
        $this->registerSubCommand(new HelpSubCommand("help", "Help Command"));
        $this->registerSubCommand(new CreateSubCommand("create", "Create arena command"));
        $this->registerSubCommand(new SetSpawnSubCommand("setspawn", "Setspawn position command"));
        $this->registerSubCommand(new SetGoalSubCommand("setgoal", "Set goal position command"));
        $this->registerSubCommand(new SetWorldSubCommand("setworld", "Set world arena"));
        $this->registerSubCommand(new JoinSubCommand("join", "Join to arena"));
        $this->registerSubCommand(new RandomJoinSubCommand("random", "Random join to arena"));
        $this->registerSubCommand(new ReloadSubCommand("reload", "Reload arenas"));
        $this->registerSubCommand(new QuitSubCommand("quit", "Quit from arena"));
        $this->registerSubCommand(new SetHubSubCommand("sethub", "Set hub arena"));
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
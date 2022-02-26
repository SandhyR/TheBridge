<?php

namespace SandhyR\TheBridge;

use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use SandhyR\TheBridge\command\TheBridgeCommand;
use SandhyR\TheBridge\game\Game;

class TheBridge extends PluginBase{

    /** @var TheBridge */
    private static TheBridge $instance;

    /** @var Game[] */
    private array $game;

    /** @return TheBridge */
    public static function getInstance(): TheBridge{
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        if(!PacketHooker::isRegistered()){
            PacketHooker::register($this);
        }
        $this->getServer()->getCommandMap()->register("thebridge", new TheBridgeCommand($this, "thebridge", "TheBridge Command", ["tb"]));
    }

    /**
     * @param string $arena
     * @return bool
     */
    public function createArena(string $arena): bool{
        if($this->getGame($arena) !== null){
            return false;
        }
        $this->game[$arena] = new Game(null,null,null,null,null, $arena);
        return true;
    }

    /**
     * @param string $name
     * @return Game|null
     */
    public function getGame(string $name): ?Game{
        return $this->game[$name] ?? null;
    }
}

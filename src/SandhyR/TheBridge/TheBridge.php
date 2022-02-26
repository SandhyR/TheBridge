<?php

namespace SandhyR\TheBridge;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use SandhyR\TheBridge\command\TheBridgeCommand;

class TheBridge extends PluginBase{

    /** @var TheBridge */
    private static TheBridge $instance;

    /** @return TheBridge */
    public static function getInstance(): self{
        return self::getInstance();
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

    /** @return bool */
    public function createArena(string $arena): bool{
        return true;
        // TODO
    }
}

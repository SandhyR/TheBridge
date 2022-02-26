<?php

namespace SandhyR\TheBridge\game;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use SandhyR\TheBridge\task\GameTask;
use SandhyR\TheBridge\TheBridge;
use SandhyR\TheBridge\utils\Utils;

class Game{

    /** @var bool */
    private bool $start = false;

    /** @var array */
    private array $arenainfo;

    /** @var Task */
    private Task $task;

    /** @var string */
    private string $phase = "OFFLINE";

    /** @var Player[] */
    private array $players;

    /**
     * @param Vector3|null $bluespawn
     * @param Vector3|null $redspawn
     * @param Vector3|null $bluegoal
     * @param Vector3|null $redgoal
     * @param string|null $worldname
     * @param string|null $arenaname
     */
    public function __construct(?Vector3 $bluespawn = null, ?Vector3 $redspawn = null, ?Vector3 $bluegoal = null, ?Vector3 $redgoal = null, ?string $worldname = null, ?string $arenaname = null)
    {
        $this->arenainfo["bluespawn"] = $bluespawn;
        $this->arenainfo["redspawn"] = $redspawn;
        $this->arenainfo["bluegoal"] = $bluegoal;
        $this->arenainfo["redgoal"] = $redgoal;
        $this->arenainfo["worldname"] = $worldname;
        $this->arenainfo["arenaname"] = $arenaname;
        if($this->isValidArena()) {
            $this->startArena();
        }
    }

    /** 
     * @return bool
     */
    public function isValidArena(): bool{
        if ($this->arenainfo["bluespawn"] instanceof Vector3 && $this->arenainfo["redspawn"] instanceof Vector3 && $this->arenainfo["bluegoal"] instanceof Vector3 && $this->arenainfo["redgoal"] instanceof Vector3 && is_string($this->arenainfo["worldname"] && is_string($this->arenainfo["arenaname"]))){
            return true;
        }
        return false;
    }

    /**
     * @param string $team
     * @param Vector3 $pos
     */
    public function setSpawnPos(string $team, Vector3 $pos){
        $this->arenainfo[$team . "spawn"] = $pos;
    }

    /**
     * @param string $team
     * @param Vector3 $pos
     */
    public function setGoalPos(string $team, Vector3 $pos){
        $this->arenainfo[$team . "goal"] = $pos;
    }

    /**
     * @param World $world
     */
    public function setWorld(World $world){
        $this->arenainfo["worldname"] = $world->getFolderName();
    }

    private function startArena(){
        $this->start = true;
        $this->phase = "LOBBY";
        TheBridge::getInstance()->getScheduler()->scheduleRepeatingTask($this->task = new GameTask($this), 20);
    }

    /**
     * @param bool $lobby
     * @return bool
     */
    public function isRunning(bool $lobby = true): bool
    {
        if ($lobby) {
            return $this->start && $this->phase == "LOBBY";
        }
        return $this->start;
    }

    /**
     * @return array
     */
    public function getArenaInfo(): array{
        $arr = [];
        foreach ($this->arenainfo as $i => $k){
            if($k instanceof Vector3){
                $arr[$i] = Utils::vectorToString($k);
            } else {
                $arr[$i] = $k;
            }
        }
        return $arr;
    }

    /**
     * Save all arena data
     * @return void
     */
    public function reload(): void{
        $config = new Config(TheBridge::getInstance()->getDataFolder() . "arenas/" . $this->arenainfo["arenaname"] . ".json", Config::JSON, $this->getArenaInfo());
        try {
            $config->save();
        } catch (\JsonException){}
        if($this->isValidArena()){
            $this->startArena();
        }
    }

    /** @return string */
    public function getName(): string{
        return $this->arenainfo["arenaname"];
    }

    public function tick(): void{
        switch ($this->phase){
            case "LOBBY":

        }
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addPlayer(Player $player): void{
        $this->players[strtolower($player->getName())] = $player;
    }

}

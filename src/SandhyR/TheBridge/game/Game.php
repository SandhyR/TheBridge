<?php

namespace SandhyR\TheBridge\game;

use pocketmine\math\Vector3;

class Game{

    /** @var array */
    private array $arenainfo;

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
}

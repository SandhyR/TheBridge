<?php

namespace SandhyR\TheBridge\task;

use pocketmine\scheduler\Task;
use SandhyR\TheBridge\game\Game;

class GameTask extends Task{

    public function __construct(private Game $game)
    {
    }

    public function onRun(): void
    {
        $this->game->tick();
    }
}

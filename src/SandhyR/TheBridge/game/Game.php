<?php

namespace SandhyR\TheBridge\game;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;
use SandhyR\TheBridge\task\GameTask;
use SandhyR\TheBridge\TheBridge;
use SandhyR\TheBridge\utils\Scoreboard;
use SandhyR\TheBridge\utils\Utils;

class Game{

    /** @var bool */
    private bool $start = false;

    /** @var array */
    private array $arenainfo;

    /** @var Task */
    private Task $task;

    /** @var string */
    public string $phase = "OFFLINE";

    /** @var Player[] */
    private array $players = [];

    /** @var Vector3[] */
    public array $placedblock = [];

    /** @var string[] */
    private array $teams = [];

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
        if (($this->arenainfo["bluespawn"] instanceof Vector3) and ($this->arenainfo["redspawn"] instanceof Vector3) and ($this->arenainfo["bluegoal"] instanceof Vector3) and ($this->arenainfo["redgoal"] instanceof Vector3) and (is_string($this->arenainfo["worldname"]) and (is_string($this->arenainfo["arenaname"])))){
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
        switch ($this->phase) {
            case "LOBBY":
                    foreach ($this->players as $player) {
                        if ($player->isOnline()) {
                            $scoreboard = Scoreboard::getInstance();
                            $scoreboard->new($player, "Thebridge", TextFormat::YELLOW . TextFormat::BOLD .  "THE BRIDGE");
                            $scoreboard->setLine($player, 1, " ");
                            $scoreboard->setLine($player, 2, TextFormat::WHITE . "Players: " . TextFormat::GREEN . count($this->players) . "/2");
                        }
                    }
                    break;
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addPlayer(Player $player): void{
        if(count($this->players) == 2){
            return;
        }

        $this->players[strtolower($player->getName())] = $player;
        $player->setGamemode(GameMode::ADVENTURE());
        $this->teams[strtolower($player->getName())] = $this->getTeam();
        $player->teleport(Position::fromObject($this->arenainfo[$this->getTeam() . "spawn"], Server::getInstance()->getWorldManager()->getWorldByName($this->arenainfo["worldname"])));
        $player->getInventory()->clearAll();
        $player->setHealth(20);
        $player->getHungerManager()->setFood(20);
        $player->getInventory()->setItem(8, VanillaItems::WHITE_BED()->setCustomName("Leave"));
        if(count($this->players) == 2){
            $this->phase = "COUNTDOWN";
        }
    }

    private function getTeam(){
        if(count($this->players) == 1){
            return "red";
        }
        return "blue";
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isInGame(Player $player): bool{
        return isset($this->players[strtolower($player->getName())]);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removePlayer(Player $player): void{
        unset($this->players[strtolower($player->getName())]);
    }

    private function stop(){
        //remove all placed block
        foreach ($this->placedblock as $pos){
            Server::getInstance()->getWorldManager()->getWorldByName($this->arenainfo["worldname"])->setBlock($pos, VanillaBlocks::AIR());
        }
    }

    public function broadcastMessage(Player $player, string $message){
        foreach ($this->players as $p){
            $p->sendMessage($this->getTeamFormat($player) . " " . TextFormat::WHITE . $player->getName() . ": " . $message);
        }
    }

    private function getTeamFormat(Player $player){
        if($this->teams[strtolower($player->getName())] == "blue"){
            return TextFormat::BLUE . "[BLUE]";
        }
        return TextFormat::RED . "[RED]";}
}

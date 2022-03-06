<?php

namespace SandhyR\TheBridge\game;

use jackmd\scorefactory\ScoreFactory;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\sound\PopSound;
use pocketmine\world\World;
use SandhyR\TheBridge\task\GameTask;
use SandhyR\TheBridge\TheBridge;
use SandhyR\TheBridge\utils\Utils;

class Game
{

    /** @var bool */
    private bool $start = false;

    /** @var array */
    private array $arenainfo;

    /** @var Task|null */
    private ?Task $task = null;

    /** @var string */
    public string $phase = "OFFLINE";

    /** @var Player[] */
    private array $players = [];

    /** @var Vector3[] */
    public array $placedblock = [];

    /** @var string[] */
    private array $teams = [];

    /** @var int */
    private int $countdown = 15;

    /** @var bool */
    private bool $cage = false;

    /** @var int */
    private int $cagecountdown = 5;

    /** @var array */
    private array $playerinfo = [];

    /** @var int */
    private int $timer = 900; //15 minutes

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
        if ($this->isValidArena()) {
            $this->startArena();
        }
    }

    /**
     * @return bool
     */
    public function isValidArena(): bool
    {
        if (($this->arenainfo["bluespawn"] instanceof Vector3) and ($this->arenainfo["redspawn"] instanceof Vector3) and ($this->arenainfo["bluegoal"] instanceof Vector3) and ($this->arenainfo["redgoal"] instanceof Vector3) and (is_string($this->arenainfo["worldname"]) and (is_string($this->arenainfo["arenaname"])))) {
            return true;
        }
        return false;
    }

    /**
     * @param string $team
     * @param Vector3 $pos
     */
    public function setSpawnPos(string $team, Vector3 $pos)
    {
        $this->arenainfo[$team . "spawn"] = $pos;
    }

    /**
     * @param string $team
     * @param Vector3 $pos
     */
    public function setGoalPos(string $team, Vector3 $pos)
    {
        $this->arenainfo[$team . "goal"] = $pos;
    }

    /**
     * @param World $world
     */
    public function setWorld(World $world)
    {
        $this->arenainfo["worldname"] = $world->getDisplayName();
    }

    private function startArena()
    {
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
    public function getArenaInfo(): array
    {
        $arr = [];
        foreach ($this->arenainfo as $i => $k) {
            if ($k instanceof Vector3) {
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
    public function reload(): void
    {
        $config = new Config(TheBridge::getInstance()->getDataFolder() . "arenas/" . $this->arenainfo["arenaname"] . ".json", Config::JSON, $this->getArenaInfo());
        try {
            $config->save();
        } catch (\JsonException) {
        }
        if ($this->isValidArena()) {
            $this->startArena();
        }
    }

    /** @return string */
    public function getName(): string
    {
        return $this->arenainfo["arenaname"];
    }

    public function tick(): void
    {
        switch ($this->phase) {
            case "LOBBY":
                foreach ($this->players as $player) {
                    if ($player->isOnline()) {
                        ScoreFactory::setObjective($player, TextFormat::YELLOW . TextFormat::BOLD . "THE BRIDGE");
                        ScoreFactory::setScoreLine($player, 1, TextFormat::WHITE . "Players: " . TextFormat::GREEN . count($this->players) . "/2");
                        ScoreFactory::setScoreLine($player, 2, TextFormat::WHITE . "Map: " . TextFormat::GREEN . $this->arenainfo["arenaname"]);
                        ScoreFactory::setScoreLine($player, 3, " ");
                        ScoreFactory::setScoreLine($player, 4, TextFormat::Red . "Waiting for more players..");
                        ScoreFactory::setScoreLine($player, 5, "      ");
                        ScoreFactory::setScoreLine($player, 6, "Mode: " . TextFormat::GREEN . "Solo");
                        ScoreFactory::setScoreLine($player, 7, "    ");
                        ScoreFactory::setScoreLine($player, 8, TextFormat::YELLOW . "play.yourservername.com");
                        ScoreFactory::sendObjective($player);
                        ScoreFactory::sendLines($player);
                    }
                }
                break;
            case "COUNTDOWN":
                foreach ($this->players as $player) {
                    if ($player->isOnline()) {
                        ScoreFactory::setObjective($player, TextFormat::YELLOW . TextFormat::BOLD . "THE BRIDGE");
                        ScoreFactory::setScoreLine($player, 1, TextFormat::WHITE . "Players: " . TextFormat::GREEN . count($this->players) . "/2");
                        ScoreFactory::setScoreLine($player, 2, TextFormat::WHITE . "Map: " . TextFormat::GREEN . $this->arenainfo["arenaname"]);
                        ScoreFactory::setScoreLine($player, 3, "    ");
                        ScoreFactory::setScoreLine($player, 4, "Starting in " . TextFormat::GREEN . $this->countdown . "s");
                        ScoreFactory::setScoreLine($player, 5, "  ");
                        ScoreFactory::setScoreLine($player, 6, "Mode: " . TextFormat::GREEN . "Solo");
                        ScoreFactory::setScoreLine($player, 7, " ");
                        ScoreFactory::setScoreLine($player, 8, TextFormat::YELLOW . "play.yourservername.com");
                        ScoreFactory::sendObjective($player);
                        ScoreFactory::sendLines($player);
                    }
                    if($this->countdown <= 5){
                        $player->sendTitle(TextFormat::YELLOW . $this->countdown);
                        $player->getWorld()->addSound($player->getPosition(),new PopSound());
                    }
                    if($this->countdown <= 0){
                        $this->phase = "RUNNING";
                        $this->respawnPlayer($player);
                        $this->sendCage($this->arenainfo[$this->getTeam($player) . "spawn"], false, 2, 0, $this->getTeam($player));
                        $this->cage = true;
                    }
                }
                --$this->countdown;
                break;
            case "RUNNING":
                foreach ($this->players as $player){
                    if($this->cage) {
                        $player->sendTitle("", TextFormat::GRAY . "Cages will open in " . TextFormat::GREEN . $this->cagecountdown);
                        $player->getWorld()->addSound($player->getPosition(), new PopSound());
                    }
                    ScoreFactory::setObjective($player, TextFormat::YELLOW . TextFormat::BOLD . "THE BRIDGE");
                    ScoreFactory::setScoreLine($player, 1, TextFormat::WHITE . "Time left: " . TextFormat::GREEN . Utils::intToString($this->timer));
                    ScoreFactory::setScoreLine($player, 2, " ");
                    ScoreFactory::setScoreLine($player, 3, TextFormat::RED . TextFormat::BOLD . "[R]" . TextFormat::RESET . Utils::intToPoint($this->playerinfo[array_search("red", $this->teams)]["goals"]));
                    ScoreFactory::setScoreLine($player, 4, TextFormat::BLUE . TextFormat::BOLD . "[B]" . TextFormat::RESET . Utils::intToPoint($this->playerinfo[array_search("blue", $this->teams)]["goals"]));
                    ScoreFactory::setScoreLine($player, 5, "   ");
                    ScoreFactory::setScoreLine($player, 6, TextFormat::WHITE . "Kills: " . TextFormat::GREEN . $this->playerinfo[strtolower($player->getName())]["kills"]);
                    ScoreFactory::setScoreLine($player, 7, TextFormat::WHITE . "Goals: " . TextFormat::GREEN . $this->playerinfo[strtolower($player->getName())]["goals"]);
                    ScoreFactory::setScoreLine($player, 8, "  ");
                    ScoreFactory::setScoreLine($player, 9, TextFormat::WHITE . "Map: §a" . $this->arenainfo["arenaname"]);
                    ScoreFactory::setScoreLine($player, 10, TextFormat::WHITE . "Mode: §aSolo");
                    ScoreFactory::setScoreLine($player, 11, " ");
                    ScoreFactory::setScoreLine($player, 12, TextFormat::YELLOW . "play.yourservername.com");
                    ScoreFactory::sendObjective($player);
                    ScoreFactory::sendLines($player);
                }
                if ($this->cagecountdown <= 0) {
                    $this->cage = false;
                    $this->cagecountdown = 5;
                    $this->removeAllCages();
                }
                if($this->cage){
                --$this->cagecountdown;
                }

                --$this->timer;
        }
    }
    /**
     * @param Player $player
     * @return void
     */
    public function addPlayer(Player $player): void
    {
        if (count($this->players) == 2) {
            return;
        }
        $this->players[strtolower($player->getName())] = $player;
        $this->playerinfo[strtolower($player->getName())] = ["kills" => 0, "goals" => 0];
        $player->setGamemode(GameMode::ADVENTURE());
        $this->setTeam($player);
        $player->teleport(Position::fromObject($this->arenainfo[$this->getTeam($player) . "spawn"], Server::getInstance()->getWorldManager()->getWorldByName($this->arenainfo["worldname"])));
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->setHealth(20);
        $player->getHungerManager()->setFood(20);
        $player->getInventory()->setItem(8, VanillaItems::WHITE_BED()->setCustomName("Leave"));
        $this->broadcastCustomMessage($player->getName() . " Joined");
        if (count($this->players) == 2) {
            $this->phase = "COUNTDOWN";
        }
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getTeam(Player $player): string
    {
        return $this->teams[strtolower($player->getName())];
    }

    /**
     * @param Player $player
     * @return void
     */
    private function setTeam(Player $player): void{
        if(count($this->teams) > 0) {
            foreach ($this->teams as $k) {
                if ($k == "red") {
                    $this->teams[strtolower($player->getName())] = "blue";
                } else {
                    $this->teams[strtolower($player->getName())] = "red";
                }
            }
        } else {
            $this->teams[strtolower($player->getName())] = "red";
        }
    }


    /**
     * @param Player $player
     * @return bool
     */
    public function isInGame(Player $player): bool
    {
        return isset($this->players[strtolower($player->getName())]);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removePlayer(Player $player): void
    {
        unset($this->players[strtolower($player->getName())], $this->teams[strtolower($player->getName())]);
        $this->checkCountdown();
    }


    /**
     * @return void
     */
    public function stop(): void
    {
        if ($this->task instanceof Task) {
            $this->task->getHandler()->cancel();
        }
        //remove all placed block
        foreach ($this->placedblock as $pos) {
            Server::getInstance()->getWorldManager()->getWorldByName($this->arenainfo["worldname"])->setBlock($pos, VanillaBlocks::AIR());
        }
        $this->placedblock = [];
        $this->teams = [];
        $this->task = null;
        $this->players = [];
    }

    public function broadcastMessage(Player $player, string $message)
    {
        foreach ($this->players as $p) {
            $p->sendMessage($this->getTeamChatFormat($player) . " " . TextFormat::WHITE . $player->getName() . ": " . $message);
        }
    }

    /**
     * @param Player $player
     * @return string
     */
    private function getTeamChatFormat(Player $player): string
    {
        if ($this->teams[strtolower($player->getName())] == "blue") {
            return TextFormat::BLUE . "[BLUE]";
        }
        return TextFormat::RED . "[RED]";
    }

    /**
     * @param Player $player
     * @return string
     */
    private function getTeamScoreFormat(Player $player): string
    {
        if ($this->teams[strtolower($player->getName())] == "blue") {
            return TextFormat::BLUE . "Blue";
        }
        return TextFormat::RED . "Red";
    }

    /**
     * @param string $message
     * @return void
     */
    public function broadcastCustomMessage(string $message): void{
        foreach ($this->players as $p) {
            $p->sendMessage($message);
        }
    }

    /**
     * @return void
     */
    private function checkCountdown(): void{
        if(count($this->players) < 2){
            if($this->phase == "COUNTDOWN"){
                $this->phase = "LOBBY";
                $this->countdown = 15;
            }
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendVictory(Player $player): void{
        // TODO
    }

    /**
     * @param Player $player
     * @param bool $survival
     * @return void
     */
    public function respawnPlayer(Player $player, bool $survival = false): void{
        $player->teleport(Position::fromObject($this->arenainfo[$this->getTeam($player) . "spawn"], Server::getInstance()->getWorldManager()->getWorldByName($this->arenainfo["worldname"])));
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        if($survival){
            $player->setGamemode(GameMode::SURVIVAL());
        } else {
            $player->setGamemode(GameMode::ADVENTURE());
        }
        $player->setHealth(20);
        $player->getArmorInventory()->setChestplate(VanillaItems::LEATHER_TUNIC()->setCustomColor(Utils::colorIntoObject($this->getTeam($player))));
        $player->getArmorInventory()->setLeggings(VanillaItems::LEATHER_PANTS()->setCustomColor(Utils::colorIntoObject($this->getTeam($player))));
        $player->getArmorInventory()->setBoots(VanillaItems::LEATHER_BOOTS()->setCustomColor(Utils::colorIntoObject($this->getTeam($player))));
        $player->getInventory()->setItem(0, VanillaItems::IRON_SWORD());
        $player->getInventory()->setItem(1, VanillaItems::BOW()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER())));
        $player->getInventory()->setItem(2, VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(),  2)));
        $player->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, Utils::teamToMeta($this->getTeam($player)), 64  * 2));
        $player->getInventory()->setItem(7, VanillaItems::GOLDEN_APPLE()->setCount(8));
        $player->getInventory()->setItem(8, VanillaItems::ARROW());
        $player->getHungerManager()->setFood(20);
    }

    /**
     * @param Vector3 $pos
     * @param bool $v
     * @param int $dis
     * @param int $ad
     * @param string|null $team
     * @return void
     */
    public function sendCage(Vector3 $pos, bool $v, int $dis, int $ad, ?string $team)
    {
        $world = Server::getInstance()->getWorldManager()->getWorldByName($this->arenainfo["worldname"]);
        $yy = $v ? 1 : 3;
        $yy += $ad;
        for ($x = $pos->getFloorX() - $dis; $x <= $pos->getFloorX() + $dis; $x++) {
            for ($y = $pos->getFloorY() + $yy; $y >= $pos->getFloorY() - 1; $y--) {
                for ($z = $pos->getFloorZ() + $dis; $z >= $pos->getFloorZ() - $dis; $z--) {
                    if($v){
                        $world->setBlockAt($x,$y,$z, VanillaBlocks::AIR());
                    } else {
                        $world->setBlockAt($x, $y, $z, BlockFactory::getInstance()->get(BlockLegacyIds::STAINED_GLASS, Utils::teamToMeta($team)));
                    }
                }
            }
        }
        if(!$v){
            $this->sendCage($pos->add(0, 1, 0), true, $dis - 1, $ad, $team);
        }
    }

    /**
     * @return void
     */
    private function removeAllCages(): void{
        $this->sendCage($this->arenainfo["bluespawn"], true, 2, 4, null);
        $this->sendCage($this->arenainfo["redspawn"], true, 2, 4, null);
        foreach ($this->players as $player){
            $player->setGamemode(GameMode::SURVIVAL());
            $player->sendTitle(TextFormat::GREEN . "FIGHT!");
        }
    }

    public function addKill(Player $player){
        ++$this->playerinfo[strtolower($player->getName())]["kills"];
    }

    public function addGoal(Player $player){
        ++$this->playerinfo[strtolower($player->getName())]["goals"];
    }

    /**
     * @return void
     */
    public function sendAllCage(): void{
        foreach ($this->players as $player){
            $this->respawnPlayer($player);
            $this->sendCage($this->arenainfo[$this->getTeam($player) . "spawn"], false, 2, 0, $this->getTeam($player));
        }
    }

    /**
     * @return array
     */
    public function getPureArenaInfo(): array{
        return $this->arenainfo;
    }
}

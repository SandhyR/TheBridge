<?php

namespace SandhyR\TheBridge;

use jackmd\scorefactory\ScoreFactory;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use SandhyR\TheBridge\game\Game;
use SandhyR\TheBridge\utils\Utils;

class EventListener implements Listener{

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game){
            $game->broadcastCustomMessage(TextFormat::RED . $player->getName() . " disconnected!");
            $game->removePlayer($player);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event){
        $player = $event->getPlayer();
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game){
            if($game->phase == "LOBBY" || $game->phase == "COUNTDOWN" || $game->phase == "RESTARTING"){
                $event->cancel();
                return;
            }

            foreach (["red","blue"] as $team){
                if($event->getBlock()->getPosition()->distance($game->getPureArenaInfo()[$team . "goal"]) < 10){
                    $event->cancel();
                    $player->sendMessage(TextFormat::RED . "You cant place block here!");
                    return;
                }
            }
            $game->placedblock[Utils::vectorToString($event->getBlock()->getPosition()->asVector3())] = $event->getBlock()->getPosition()->asVector3();
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game){
            if(isset($game->placedblock[Utils::vectorToString($event->getBlock()->getPosition()->asVector3())])){
                unset($game->placedblock[Utils::vectorToString($event->getBlock()->getPosition()->asVector3())]);
            } else {
                $event->cancel();
            }
        }
    }
    /**
     * @param PlayerExhaustEvent $event
     */
    public function onExhaust(PlayerExhaustEvent $event){
        $player = $event->getPlayer();
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game){
            $event->cancel();
            $event->getPlayer()->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event){
        $player = $event->getEntity();
        if($player instanceof Player) {
            if (($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game) {
                if($game->phase == "LOBBY" || $game->phase == "COUNTDOWN" || $game->phase == "RESTARTING"){
                    $event->cancel();
                    return;
                }
                if($event->getCause() == $event::CAUSE_FALL){
                    $event->cancel();
                    return;
                }
                if($event instanceof EntityDamageByEntityEvent) {
                    if (($damager = $event->getDamager()) instanceof Player && $game->isInGame($damager))
                        $game->playerinfo[strtolower($player->getName())]["damager"] = $damager;
                        TheBridge::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                            function() use($player, $game){
                                $game->playerinfo[strtolower($player->getName())]["damager"] = null;
                            }
                        ), 20 * 5);
                    if ($event->getFinalDamage() >= $player->getHealth()) {
                        $game->handleDeath($player, $event);
                        $event->cancel();
                    }
                }
            }
        }
    }

    public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game) {
            $game->broadcastMessage($player, $event->getMessage());
            $event->cancel();
        }
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game) {
            if($game->phase !== "RUNNING"){
                return;
            }
            /** @var Vector3 $owngoal */
            $owngoal = $game->getPureArenaInfo()[$game->getTeam($player) . "goal"];
            /** @var Vector3 $enemygoal */
            $enemygoal = $game->getPureArenaInfo()[Utils::getEnemyTeam($game->getTeam($player)) . "goal"];
            if($player->getLocation()->distance($owngoal) <= 3){
                $player->sendMessage(TextFormat::RED . "You cant score to own goal!");
                $game->respawnPlayer($player, true);
                return;
            }

            if($player->getLocation()->distance($enemygoal) <= 3) {
                if ($game->addGoal($player)) {
                    $game->scoredname = $player->getName();
                    $game->sendAllCages();
                }
            }
        }
    }

    public function onUse(PlayerItemUseEvent $event){
        $player = $event->getPlayer();
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game) {
            if($event->getItem()->getId() == ItemIds::BED){
                $game->removePlayer($player);
                ScoreFactory::removeObjective($player);
                $player->teleport($game->getHub());
                $player->getInventory()->clearAll();
            }
        }
    }
}

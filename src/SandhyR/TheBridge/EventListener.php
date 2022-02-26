<?php

namespace SandhyR\TheBridge;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use SandhyR\TheBridge\game\Game;
use SandhyR\TheBridge\utils\Scoreboard;
use SandhyR\TheBridge\utils\Utils;

class EventListener implements Listener{

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if(isset(Scoreboard::getInstance()->scoreboards[$player->getName()])) unset(Scoreboard::getInstance()->scoreboards[$player->getName()]);
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game){
            $game->removePlayer($player);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event){
        $player = $event->getPlayer();
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game){
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
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event){
        $player = $event->getEntity();
        if($player instanceof Player) {
            if (($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game) {
                if($game->phase == "LOBBY" || $game->phase == "COUNTDOWN"){
                    $event->cancel();
                }
            }
        }
    }

    public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        if(($game = TheBridge::getInstance()->getPlayerGame($player)) instanceof Game){
            $game->broadcastMessage($player, $event->getMessage());
            $event->cancel();
        }
    }
}

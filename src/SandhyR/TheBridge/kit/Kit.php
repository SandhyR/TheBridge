<?php

declare(strict_types=1);


namespace SandhyR\TheBridge\kit;


use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

abstract class Kit {

    protected Layout $layout;

    public function __construct(Layout $layout) {
        $this->layout = $layout;
    }

    public function getLayout(): Layout {
        return $this->layout;
    }

    /**
     * @return Armor[]
     */
    abstract public function getArmorContents(DyeColor $color): array;

    /**
     * @return Item[]
     */
    abstract public function getItems(DyeColor $color): array; // TODO: Change the function name?

}
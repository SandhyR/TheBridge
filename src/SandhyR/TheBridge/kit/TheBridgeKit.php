<?php

declare(strict_types=1);


namespace SandhyR\TheBridge\kit;


use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\item\Armor;

class TheBridgeKit extends Kit {

    public function getArmorContents(DyeColor $color): array {
        $unbreaking = new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 10);
        $color = $color->getRgbValue();
        return [
			VanillaBlocks::AIR()->asItem(),
            VanillaItems::LEATHER_TUNIC()->setCustomColor($color)->addEnchantment($unbreaking),
            VanillaItems::LEATHER_PANTS()->setCustomColor($color)->addEnchantment($unbreaking),
            VanillaItems::LEATHER_BOOTS()->setCustomColor($color)->addEnchantment($unbreaking)
        ];
    }

    public function getItems(DyeColor $color): array {
        $items = [];
        foreach($this->layout->getBlocksSlots() as $slot => $amount) {
            $items[$slot] = BlockFactory::getInstance()->get(
                BlockLegacyIds::TERRACOTTA,
                DyeColorIdMap::getInstance()->toId($color)
            )->asItem()->setCount($amount);
        }
        foreach($this->layout->getGapplesSlots() as $slot => $amount) {
            $items[$slot] = VanillaItems::GOLDEN_APPLE()->setCount($amount);
        }
        $items[$this->layout->getSwordSlot()] = VanillaItems::IRON_SWORD();
        $items[$this->layout->getBowSlot()] = VanillaItems::BOW();
        $items[$this->layout->getPickaxeSlot()] = VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2));
        $items[$this->layout->getArrowSlot()] = VanillaItems::ARROW();

        return $items;
    }

}

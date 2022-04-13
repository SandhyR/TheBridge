<?php

declare(strict_types=1);


namespace SandhyR\TheBridge\kit;

use SandhyR\TheBridge\kit\Kit;
use SandhyR\TheBridge\kit\TheBridgeKit;

class KitFactory {

    /** @var Kit[] */
    static private array $kits = [];

    static public function init(): void {
        self::registerKit(new TheBridgeKit());
    }

    static public function getKitById(int $id): ?Kit {
        return self::$kits[$id] ?? null;
    }

    static private function registerKit(Kit $kit): void {
        self::$kits[$kit->getId()] = $kit;
    }

}

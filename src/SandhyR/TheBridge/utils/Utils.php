<?php

namespace SandhyR\TheBridge\utils;

use pocketmine\math\Vector3;

class Utils{

    /**
     * @param Vector3 $vector
     * @return string
     */
    public static function vectorToString(Vector3 $vector): string{
        return $vector->getX() . ":" . $vector->getY() . ":" . $vector->getZ();
    }

    /**
     * @param string $delimeter
     * @param string $string
     * @return Vector3
     */
    public static function stringToVector(string $delimeter, ?string $string): ?Vector3
    {
        if($string !== null) {
            $split = explode($delimeter, $string);
            return new Vector3($split[0], $split[1], $split[2]);
        }
        return null;
    }
}
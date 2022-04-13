<?php

declare(strict_types=1);


namespace SandhyR\TheBridge\kit;


class Layout {

    private int $sword_slot;
    private int $bow_slot;
    private int $pickaxe_slot;
    private int $arrow_slot;

    private array $blocks_slots;
    private array $gapples_slots;

    public function __construct(int $sword_slot, int $bow_slot, int $pickaxe_slot, int $arrow_slot, array $blocks_slots, array $gapples_slots) {
        $this->sword_slot = $sword_slot;
        $this->bow_slot = $bow_slot;
        $this->pickaxe_slot = $pickaxe_slot;
        $this->arrow_slot = $arrow_slot;
        $this->blocks_slots = $blocks_slots;
        $this->gapples_slots = $gapples_slots;
    }

    static public function fromData(array $data): self {
        return new self($data["sword_slot"], $data["bow_slot"], $data["pickaxe_slot"], $data["arrow_slot"], $data["blocks_slots"], $data["gapples_slots"]);
    }

    public function setSwordSlot(int $sword_slot): void {
        $this->sword_slot = $sword_slot;
    }

    public function setBowSlot(int $bow_slot): void {
        $this->bow_slot = $bow_slot;
    }

    public function setPickaxeSlot(int $pickaxe_slot): void {
        $this->pickaxe_slot = $pickaxe_slot;
    }

    public function setArrowSlot(int $arrow_slot): void {
        $this->arrow_slot = $arrow_slot;
    }

    public function setBlocksSlots(array $blocks_slots): void {
        $this->blocks_slots = $blocks_slots;
    }

    public function setGapplesSlots(array $gapples_slots): void {
        $this->gapples_slots = $gapples_slots;
    }

    public function getSwordSlot(): int {
        return $this->sword_slot;
    }

    public function getBowSlot(): int {
        return $this->bow_slot;
    }

    public function getPickaxeSlot(): int {
        return $this->pickaxe_slot;
    }

    public function getArrowSlot(): int {
        return $this->arrow_slot;
    }

    public function getBlocksSlots(): array {
        return $this->blocks_slots;
    }

    public function getGapplesSlots(): array {
        return $this->gapples_slots;
    }

    static public function switchUIIndexToPlayerInventoryIndex(int $index): int {
        return match($index) {
            0 => 9,
            1 => 10,
            2 => 11,
            3 => 12,
            4 => 13,
            5 => 14,
            6 => 15,
            7 => 16,
            8 => 17,
            9 => 18,
            10 => 19,
            11 => 20,
            12 => 21,
            13 => 22,
            14 => 23,
            15 => 24,
            16 => 25,
            17 => 26,
            18 => 27,
            19 => 28,
            20 => 29,
            21 => 30,
            22 => 31,
            23 => 32,
            24 => 33,
            25 => 34,
            26 => 35,
            36 => 0,
            37 => 1,
            38 => 2,
            39 => 3,
            40 => 4,
            41 => 5,
            42 => 6,
            43 => 7,
            44 => 8,
            default => 100
        };
    }

    static public function switchPlayerInventoryIndexToUIIndex(int $index): int {
        return match($index) {
            0 => 36,
            1 => 37,
            2 => 38,
            3 => 39,
            4 => 40,
            5 => 41,
            6 => 42,
            7 => 43,
            8 => 44,
            9 => 0,
            10 => 1,
            11 => 2,
            12 => 3,
            13 => 4,
            14 => 5,
            15 => 6,
            16 => 7,
            17 => 8,
            18 => 9,
            19 => 10,
            20 => 11,
            21 => 12,
            22 => 13,
            23 => 14,
            24 => 15,
            25 => 16,
            26 => 17,
            27 => 18,
            28 => 19,
            29 => 20,
            30 => 21,
            31 => 22,
            32 => 23,
            33 => 24,
            34 => 25,
            35 => 26,
            default => 100
        };
    }

    /*
     * 0 => 36,
            1 => 37,
            2 => 38,
            3 => 39,
            4 => 40,
            5 => 41,
            6 => 42,
            7 => 43,
            8 => 44,

            36 => 0,
            37 => 1,
            38 => 2,
            39 => 3,
            40 => 4, // 18 - 27
            41 => 5,
            43 => 7,
            42 => 6,
            44 => 8
     */

}
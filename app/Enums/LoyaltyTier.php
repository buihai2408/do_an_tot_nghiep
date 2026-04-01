<?php

namespace App\Enums;

enum LoyaltyTier: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';
    case Diamond = 'diamond';

    public function label(): string
    {
        return match ($this) {
            self::Bronze => 'Đồng',
            self::Silver => 'Bạc',
            self::Gold => 'Vàng',
            self::Diamond => 'Kim cương',
        };
    }

    public function minPoints(): int
    {
        return match ($this) {
            self::Bronze => 0,
            self::Silver => 50,
            self::Gold => 200,
            self::Diamond => 500,
        };
    }

    public function multiplier(): float
    {
        return match ($this) {
            self::Bronze => 1.0,
            self::Silver => 1.2,
            self::Gold => 1.5,
            self::Diamond => 2.0,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Bronze => 'amber',
            self::Silver => 'gray',
            self::Gold => 'yellow',
            self::Diamond => 'cyan',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Bronze => '🥉',
            self::Silver => '🥈',
            self::Gold => '🥇',
            self::Diamond => '💎',
        };
    }

    public function nextTier(): ?self
    {
        return match ($this) {
            self::Bronze => self::Silver,
            self::Silver => self::Gold,
            self::Gold => self::Diamond,
            self::Diamond => null,
        };
    }

    public static function fromPoints(int $totalPoints): self
    {
        return match (true) {
            $totalPoints >= self::Diamond->minPoints() => self::Diamond,
            $totalPoints >= self::Gold->minPoints() => self::Gold,
            $totalPoints >= self::Silver->minPoints() => self::Silver,
            default => self::Bronze,
        };
    }
}

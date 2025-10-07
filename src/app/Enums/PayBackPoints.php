<?php
namespace App\Enums;

enum PayBackPoints: string
{
    case Bronze = '0';
    case Silver = '1';
    case Gold = '2';

    public function MembersRankPoint(): float
    {
        return match ($this) {
            self::Bronze => 0.01,
            self::Silver => 0.05,
            self::Gold => 0.1,
        };
    }
}
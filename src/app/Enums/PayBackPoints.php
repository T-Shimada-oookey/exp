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

    /**
     * 累計購入額に対応するランクを返す
     * Bronze: ¥0     〜 ¥9,999
     * Silver: ¥10,000 〜 ¥49,999
     * Gold:   ¥50,000 以上
     */
    public static function fromTotalAmount(int $totalAmount): self
    {
        return match (true) {
            $totalAmount >= 50000 => self::Gold,
            $totalAmount >= 10000 => self::Silver,
            default               => self::Bronze,
        };
    }
}
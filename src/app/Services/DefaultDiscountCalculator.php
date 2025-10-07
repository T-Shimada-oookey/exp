<?php
namespace App\Services;

use App\Contracts\DiscountCalculatorInterface;

class DefaultDiscountCalculator implements DiscountCalculatorInterface
{
    /**
     * 注文金額に応じた割引額を計算する
     *
     * @param float $amount 注文金額
     * @return float 割引額
    */
    public function calculateDiscount(float $amount): float
    {
        if ($amount >= 1000) {
            return $amount * 0.10;
        }
        return $amount * 0.05;
    }
}

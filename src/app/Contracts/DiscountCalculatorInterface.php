<?php
namespace App\Contracts;

interface DiscountCalculatorInterface
{
   /**
    * 注文金額に応じた割引額を計算する
    *
    * @param float $amount 注文金額
    * @return float 割引額
    */
   public function calculateDiscount(float $amount): float;
}

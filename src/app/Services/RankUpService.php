<?php
namespace App\Services;

use App\Enums\PayBackPoints;
use App\Models\User;

class RankUpService
{
    /**
     * 購入額を累計に加算し、ランクを自動更新する
     *
     * @param User $user      対象ユーザー
     * @param int  $amount    今回の購入額（円）
     * @return bool           ランクアップが発生した場合 true
     */
    public function addPurchaseAndUpdateRank(User $user, int $amount): bool
    {
        $previousRank = $user->member_rank;

        $user->total_purchase_amount += $amount;
        $user->member_rank = PayBackPoints::fromTotalAmount($user->total_purchase_amount);
        $user->save();

        return $user->member_rank !== $previousRank;
    }
}

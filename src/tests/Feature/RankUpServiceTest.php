<?php

namespace Tests\Feature;

use App\Enums\PayBackPoints;
use App\Models\User;
use App\Services\RankUpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class RankUpServiceTest extends TestCase
{
    use RefreshDatabase;

    private RankUpService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RankUpService();
    }

    #[Test]
    #[Group('rank')]
    #[TestDox('累計購入額が¥10,000に達するとBronzeからSilverへランクアップする')]
    public function it_ranks_up_from_bronze_to_silver_at_10000(): void
    {
        $user = User::factory()->create([
            'member_rank'           => PayBackPoints::Bronze,
            'total_purchase_amount' => 9000,
        ]);

        $ranked = $this->service->addPurchaseAndUpdateRank($user, 1000);

        $this->assertTrue($ranked);
        $this->assertEquals(PayBackPoints::Silver, $user->fresh()->member_rank);
        $this->assertEquals(10000, $user->fresh()->total_purchase_amount);
    }

    #[Test]
    #[Group('rank')]
    #[TestDox('累計購入額が¥50,000に達するとSilverからGoldへランクアップする')]
    public function it_ranks_up_from_silver_to_gold_at_50000(): void
    {
        $user = User::factory()->create([
            'member_rank'           => PayBackPoints::Silver,
            'total_purchase_amount' => 45000,
        ]);

        $ranked = $this->service->addPurchaseAndUpdateRank($user, 5000);

        $this->assertTrue($ranked);
        $this->assertEquals(PayBackPoints::Gold, $user->fresh()->member_rank);
        $this->assertEquals(50000, $user->fresh()->total_purchase_amount);
    }

    #[Test]
    #[Group('rank')]
    #[TestDox('累計購入額が閾値未満の場合はランクアップしない')]
    public function it_does_not_rank_up_below_threshold(): void
    {
        $user = User::factory()->create([
            'member_rank'           => PayBackPoints::Bronze,
            'total_purchase_amount' => 0,
        ]);

        $ranked = $this->service->addPurchaseAndUpdateRank($user, 5000);

        $this->assertFalse($ranked);
        $this->assertEquals(PayBackPoints::Bronze, $user->fresh()->member_rank);
        $this->assertEquals(5000, $user->fresh()->total_purchase_amount);
    }

    #[Test]
    #[Group('rank')]
    #[TestDox('Goldランクのユーザーは購入後もGoldのままで戻り値はfalse')]
    public function it_stays_gold_and_returns_false_for_gold_user(): void
    {
        $user = User::factory()->create([
            'member_rank'           => PayBackPoints::Gold,
            'total_purchase_amount' => 100000,
        ]);

        $ranked = $this->service->addPurchaseAndUpdateRank($user, 10000);

        $this->assertFalse($ranked);
        $this->assertEquals(PayBackPoints::Gold, $user->fresh()->member_rank);
        $this->assertEquals(110000, $user->fresh()->total_purchase_amount);
    }

    #[Test]
    #[Group('rank')]
    #[TestDox('複数回購入を重ねてBronzeからGoldへランクアップできる')]
    public function it_can_rank_up_from_bronze_to_gold_via_multiple_purchases(): void
    {
        $user = User::factory()->create([
            'member_rank'           => PayBackPoints::Bronze,
            'total_purchase_amount' => 0,
        ]);

        $this->service->addPurchaseAndUpdateRank($user, 10000); // → Silver
        $user->refresh();
        $this->assertEquals(PayBackPoints::Silver, $user->member_rank);

        $this->service->addPurchaseAndUpdateRank($user, 40000); // → Gold
        $user->refresh();
        $this->assertEquals(PayBackPoints::Gold, $user->member_rank);
        $this->assertEquals(50000, $user->total_purchase_amount);
    }
}

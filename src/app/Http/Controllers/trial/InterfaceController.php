<?php

namespace App\Http\Controllers\trial;

use App\Http\Controllers\Controller;
use App\Enums\PayBackPoints;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Http\Response;

use App\Contracts\DiscountCalculatorInterface;


class InterfaceController extends Controller
{
    protected $discountCalculator;

    public function __construct(DiscountCalculatorInterface $discountCalculator)
    {
        $this->discountCalculator = $discountCalculator;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): view
    {
        // 会員ランクの一覧表示
        $member_ranks = PayBackPoints::cases();
        
        // ユーザーの会員ランクとポイント還元率
        $user = User::find(1);
        $userRank = $user->member_rank->name;
        $userPoint = $user->member_rank->MembersRankPoint();

        return view('trial.interface', ['member_ranks' => $member_ranks, 'userRank' => $userRank, 'userPoint' => $userPoint]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * 注文金額から割引額を計算して返すアクション
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): Response
    {
        if($request->input('amount')){
            $amount = (float)$request->input('amount');
            $discount = $this->discountCalculator->calculateDiscount($amount);
            return response()->view('trial.interface', ['discount' => $discount]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

}

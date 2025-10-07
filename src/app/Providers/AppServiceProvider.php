<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\DiscountCalculatorInterface;
use App\Services\DefaultDiscountCalculator;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DiscountCalculatorInterface::class, DefaultDiscountCalculator::class);
    }
}

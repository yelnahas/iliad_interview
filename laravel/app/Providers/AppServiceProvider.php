<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\OrderInterface;
use App\Contracts\ApiResponseInterface;
use App\Contracts\Repositories\StockRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Repositories\StockRepository;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use App\Services\ApiResponse;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderInterface::class, OrderService::class);
        $this->app->bind(ApiResponseInterface::class, ApiResponse::class);
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

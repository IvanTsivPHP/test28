<?php

namespace App\Providers;

use App\Repositories\CarRepository;
use App\Repositories\CarRepositoryInterface;
use App\Repositories\CarModelRepository;
use App\Repositories\CarModelRepositoryInterface;
use App\Repositories\BrandRepository;
use App\Repositories\BrandRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CarRepositoryInterface::class, CarRepository::class);
        $this->app->bind(CarModelRepositoryInterface::class, CarModelRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

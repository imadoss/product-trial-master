<?php

namespace App\Providers;

use App\Repositories\DbRepository;
use App\Repositories\Product\DbProductRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Repository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            Repository::class,
            DbRepository::class,
        );
        $this->app->bind(
            ProductRepository::class,
            DbProductRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}

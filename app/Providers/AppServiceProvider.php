<?php

namespace App\Providers;

use App\Models\Cat\Contracts\ICatDatabaseRepository;
use App\Models\Cat\Repositories\CatDatabaseRepository;
use App\UseCases\UseCaseFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UseCaseFactory::class);
        $this->app->bind(ICatDatabaseRepository::class, CatDatabaseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

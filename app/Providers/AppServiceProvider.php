<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();
                \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'mezzo' => \App\Models\Mezzo::class,
            'dipendente' => \App\Models\Dipendente::class,
        ]);
    }
}

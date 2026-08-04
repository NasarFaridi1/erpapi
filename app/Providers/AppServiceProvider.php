<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
    use Illuminate\Database\Eloquent\Relations\Relation;


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


public function boot()
{
    Relation::morphMap([
        'institute' => \App\Models\Institute::class,
        'company'   => \App\Models\Company::class,
        'user'      => \App\Models\User::class,
    ]);
}

    // public function boot(): void
    // {
    //     //
    // }
}

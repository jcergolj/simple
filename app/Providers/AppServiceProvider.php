<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    public function register(): void
    {
        //
    }

    /** Bootstrap any application services. */
    public function boot(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());

        Model::unguard();

        Model::preventSilentlyDiscardingAttributes();

        Model::preventLazyLoading(! $this->app->isProduction());

        Model::preventAccessingMissingAttributes();

        if (! $this->app->isLocal() && ! $this->app->environment('testing')) {
            URL::forceScheme('https');
        }

        $this->loadViewsFrom(resource_path('turbo'), 'turbo');
    }
}

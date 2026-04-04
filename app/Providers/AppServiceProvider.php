<?php

namespace App\Providers;

use App\Socialite\DynamicProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Ollama service
        $this->app->singleton('ollama', function ($app) {
            return new \App\Services\OllamaService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register dynamic SSO provider
        if ($provider = env('SSO_PROVIDER')) {
            Socialite::extend($provider, function ($app) use ($provider) {
                $config = config("services.{$provider}");

                return Socialite::buildProvider(DynamicProvider::class, $config);
            });
        }
    }
}

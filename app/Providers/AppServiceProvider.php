<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Llm\AnthropicClient;
use App\Services\Llm\LlmClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LlmClient::class, AnthropicClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

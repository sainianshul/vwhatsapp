<?php

namespace App\Providers;

use App\Contracts\SocialSearchInterface;
use App\Services\SocialSearchService;
use Illuminate\Support\ServiceProvider;

class SocialServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SocialSearchInterface::class, SocialSearchService::class);
    }

    public function boot(): void
    {
        //
    }
}

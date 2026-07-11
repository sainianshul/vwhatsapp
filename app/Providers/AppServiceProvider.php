<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Models\LoginHistory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Contracts\LlmServiceInterface::class, function ($app) {
            $provider = config('services.llm.default', 'gemini');
            
            if ($provider === 'gemini') {
                return new \App\Services\Llm\GeminiService();
            }
            
            // Fallback
            return new \App\Services\Llm\GeminiService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (Login $event) {
            $user = $event->user;
            
            // Just basic tracking without Jenssegers for now to avoid installing another package, 
            // since we just need simple platform/device info
            $userAgent = request()->userAgent();
            $deviceType = str_contains(strtolower($userAgent), 'mobile') ? 'Mobile' : 'Desktop';
            
            $platform = 'Unknown';
            if (str_contains(strtolower($userAgent), 'windows')) $platform = 'Windows';
            elseif (str_contains(strtolower($userAgent), 'mac')) $platform = 'Mac';
            elseif (str_contains(strtolower($userAgent), 'linux')) $platform = 'Linux';
            elseif (str_contains(strtolower($userAgent), 'iphone') || str_contains(strtolower($userAgent), 'ipad')) $platform = 'iOS';
            elseif (str_contains(strtolower($userAgent), 'android')) $platform = 'Android';

            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'device_type' => $deviceType,
                'platform' => $platform,
            ]);
        });
    }
}

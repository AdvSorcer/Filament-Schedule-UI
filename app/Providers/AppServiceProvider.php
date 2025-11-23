<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
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
        // 設定應用程式語言（事件監聽器已由 FilamentScheduleUIServiceProvider 處理）
        App::setLocale(config('filament-schedule-ui.locale', 'zh_TW'));
    }
}

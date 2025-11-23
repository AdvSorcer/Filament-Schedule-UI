<?php

namespace App\Providers;

use App\Listeners\LogScheduledTaskFailed;
use App\Listeners\LogScheduledTaskFinished;
use App\Listeners\LogScheduledTaskSkipped;
use App\Listeners\LogScheduledTaskStarting;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
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
        // 設定應用程式語言
        App::setLocale(config('filament-schedule-ui.locale', 'zh_TW'));

        Event::listen(ScheduledTaskStarting::class, LogScheduledTaskStarting::class);
        Event::listen(ScheduledTaskFinished::class, LogScheduledTaskFinished::class);
        Event::listen(ScheduledTaskFailed::class, LogScheduledTaskFailed::class);
        Event::listen(ScheduledTaskSkipped::class, LogScheduledTaskSkipped::class);
    }
}

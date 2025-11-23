<?php

namespace AdvSorcer\FilamentScheduleUI;

use AdvSorcer\FilamentScheduleUI\Console\Commands\SyncSchedulesCommand;
use AdvSorcer\FilamentScheduleUI\Listeners\LogScheduledTaskFailed;
use AdvSorcer\FilamentScheduleUI\Listeners\LogScheduledTaskFinished;
use AdvSorcer\FilamentScheduleUI\Listeners\LogScheduledTaskSkipped;
use AdvSorcer\FilamentScheduleUI\Listeners\LogScheduledTaskStarting;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class FilamentScheduleUIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/filament-schedule-ui.php',
            'filament-schedule-ui'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 發布配置檔案
        $this->publishes([
            __DIR__.'/../config/filament-schedule-ui.php' => config_path('filament-schedule-ui.php'),
        ], 'filament-schedule-ui-config');

        // 發布遷移檔案
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filament-schedule-ui-migrations');

        // 發布語言檔案
        $this->publishes([
            __DIR__.'/../lang' => lang_path('vendor/filament-schedule-ui'),
        ], 'filament-schedule-ui-lang');

        // 發布視圖檔案
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/filament-schedule-ui'),
        ], 'filament-schedule-ui-views');

        // 載入語言檔案（從 package 目錄）
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'schedule');

        // 載入視圖檔案（從 package 目錄）
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament');

        // 註冊 Artisan 命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncSchedulesCommand::class,
            ]);
        }

        // 註冊事件監聽器
        Event::listen(ScheduledTaskStarting::class, LogScheduledTaskStarting::class);
        Event::listen(ScheduledTaskFinished::class, LogScheduledTaskFinished::class);
        Event::listen(ScheduledTaskFailed::class, LogScheduledTaskFailed::class);
        Event::listen(ScheduledTaskSkipped::class, LogScheduledTaskSkipped::class);

        // 設定應用程式語言（如果配置中有設定）
        $locale = config('filament-schedule-ui.locale');
        if ($locale) {
            app()->setLocale($locale);
        }
    }
}


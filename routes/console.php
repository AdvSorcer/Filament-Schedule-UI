<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 測試排程：每天早上 8 點執行
Schedule::command('say:hi')
    ->dailyAt('08:00')
    ->description('每天早上 8 點說 Hi');

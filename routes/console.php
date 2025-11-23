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

// 測試排程：每天早上 7 點說早安
Schedule::command('say:good-morning')
    ->dailyAt('07:00')
    ->description('每天早上 7 點說早安');

// 測試排程：每天中午 12 點該吃午餐
Schedule::command('say:lunch-time')
    ->dailyAt('12:00')
    ->description('每天中午 12 點該吃午餐');

// 測試排程：每天下午 1 點說午安
Schedule::command('say:good-afternoon')
    ->dailyAt('13:00')
    ->description('每天下午 1 點說午安');

// 測試排程：每天晚上 6 點該吃晚餐
Schedule::command('say:dinner-time')
    ->dailyAt('18:00')
    ->description('每天晚上 6 點該吃晚餐');

// 測試排程：每天晚上 9 點說晚安
Schedule::command('say:good-evening')
    ->dailyAt('21:00')
    ->description('每天晚上 9 點說晚安');

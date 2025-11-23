<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 使用 schedule_command() helper 函數，會自動檢查 is_active 狀態
// 不需要手動加上 ->when() 條件！

// 測試排程：每十分鐘執行一次
schedule_command('say:hi')
    ->everyMinute()
    ->description('每十分鐘說 Hi');

// 測試排程：每十分鐘執行一次
schedule_command('say:good-morning')
    ->everyTenMinutes()
    ->description('每十分鐘說早安');

// 測試排程：每十分鐘執行一次
schedule_command('say:lunch-time')
    ->everyTenMinutes()
    ->description('每十分鐘該吃午餐');

// 測試排程：每十分鐘執行一次
schedule_command('say:good-afternoon')
    ->everyTenMinutes()
    ->description('每十分鐘說午安');

// 測試排程：每十分鐘執行一次
schedule_command('say:dinner-time')
    ->everyTenMinutes()
    ->description('每十分鐘該吃晚餐');

// 測試排程：每十分鐘執行一次
schedule_command('say:good-evening')
    ->everyTenMinutes()
    ->description('每十分鐘說晚安');

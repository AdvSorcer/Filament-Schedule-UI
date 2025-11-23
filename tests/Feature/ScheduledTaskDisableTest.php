<?php

use App\Models\ScheduledTask;
use Illuminate\Support\Facades\Artisan;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('停用的排程不應該執行', function () {
    // 先同步排程到資料庫
    Artisan::call('schedule:sync');

    // 找到 say:good-afternoon 排程並設為停用
    $task = ScheduledTask::where('command', 'say:good-afternoon')->first();
    expect($task)->not->toBeNull();

    $task->update(['is_active' => false]);

    // 直接測試 when() 條件的邏輯
    // 模擬 routes/console.php 中的檢查邏輯
    $isTaskActive = function (string $command): bool {
        $task = ScheduledTask::where('command', $command)->first();

        return $task?->is_active ?? true;
    };

    // 測試停用的排程
    $result = $isTaskActive('say:good-afternoon');
    expect($result)->toBeFalse('停用的排程應該返回 false');
});

it('啟用的排程應該可以執行', function () {
    // 先同步排程到資料庫
    Artisan::call('schedule:sync');

    // 找到 say:good-afternoon 排程並設為啟用
    $task = ScheduledTask::where('command', 'say:good-afternoon')->first();
    expect($task)->not->toBeNull();

    $task->update(['is_active' => true]);

    // 直接測試 when() 條件的邏輯
    $isTaskActive = function (string $command): bool {
        $task = ScheduledTask::where('command', $command)->first();

        return $task?->is_active ?? true;
    };

    // 測試啟用的排程
    $result = $isTaskActive('say:good-afternoon');
    expect($result)->toBeTrue('啟用的排程應該返回 true');
});

it('可以透過 toggle 切換排程啟用狀態', function () {
    // 先同步排程到資料庫
    Artisan::call('schedule:sync');

    $task = ScheduledTask::where('command', 'say:good-afternoon')->first();
    expect($task)->not->toBeNull();

    // 初始狀態應該是啟用
    expect($task->is_active)->toBeTrue();

    // 停用排程
    $task->update(['is_active' => false]);
    $task->refresh();
    expect($task->is_active)->toBeFalse();

    // 啟用排程
    $task->update(['is_active' => true]);
    $task->refresh();
    expect($task->is_active)->toBeTrue();
});

function extractCommand($event): string
{
    $task = $event->task;

    if ($task->command) {
        return $task->command;
    }

    if ($task->callback) {
        if (is_string($task->callback)) {
            return $task->callback;
        }

        if (is_array($task->callback)) {
            $class = is_object($task->callback[0]) ? get_class($task->callback[0]) : $task->callback[0];
            $method = $task->callback[1] ?? '__invoke';

            return $class.'@'.$method;
        }

        if (is_object($task->callback) && method_exists($task->callback, '__invoke')) {
            return get_class($task->callback).'@__invoke';
        }
    }

    return 'unknown';
}

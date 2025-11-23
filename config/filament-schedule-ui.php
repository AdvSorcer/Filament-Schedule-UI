<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | 指定排程任務的存儲驅動程式。
    | 可選值: 'database', 'redis'
    |
    */
    'storage_driver' => env('FILAMENT_SCHEDULE_UI_STORAGE_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Lock Driver
    |--------------------------------------------------------------------------
    |
    | 指定分散式鎖的驅動程式。
    | 可選值: 'database', 'redis'
    |
    */
    'lock_driver' => env('FILAMENT_SCHEDULE_UI_LOCK_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Redis Configuration
    |--------------------------------------------------------------------------
    |
    | Redis 相關配置（預留，供未來擴充使用）
    |
    */
    'redis' => [
        'connection' => env('FILAMENT_SCHEDULE_UI_REDIS_CONNECTION', 'default'),
        'prefix' => env('FILAMENT_SCHEDULE_UI_REDIS_PREFIX', 'schedule_ui:'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale Configuration
    |--------------------------------------------------------------------------
    |
    | 指定 Filament Schedule UI 的預設語言。
    | 可選值: 'zh_TW' (繁體中文), 'en' (英文)
    |
    */
    'locale' => env('FILAMENT_SCHEDULE_UI_LOCALE', 'zh_TW'),
];

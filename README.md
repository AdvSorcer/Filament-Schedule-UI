# Filament Schedule UI

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4.svg)](https://www.php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Tests](https://github.com/AdvSorcer/Filament-Schedule-UI/actions/workflows/tests.yml/badge.svg)](https://github.com/AdvSorcer/Filament-Schedule-UI/actions)

繁體中文 | [English](README_EN.md)

一個基於 Filament 的 Laravel 排程管理工具，讓您可以在後台介面中輕鬆管理和監控所有排程任務。

![排程列表](docs/images/ui-1.png)

執行訊息

![執行訊息](docs/images/ui-2.png)


## 系統需求

- PHP >= 8.3
- Laravel >= 12.0
- Composer

## 安裝步驟

### 1. 安裝 Package

使用 Composer 安裝：

```bash
composer require advsorcer/filament-schedule-ui
```

### 2. 發布配置檔案

發布配置檔案到你的專案：

```bash
php artisan vendor:publish --tag=filament-schedule-ui-config
```

這會將配置檔案發布到 `config/filament-schedule-ui.php`。

### 3. 發布並執行遷移

發布遷移檔案並執行：

```bash
php artisan vendor:publish --tag=filament-schedule-ui-migrations
php artisan migrate
```

### 4. 發布語言檔案（可選）

如果需要自訂語言檔案：

```bash
php artisan vendor:publish --tag=filament-schedule-ui-lang
```

### 5. 在 Filament Panel 中註冊資源

在你的 Filament Panel Provider（通常是 `app/Providers/Filament/AdminPanelProvider.php`）中註冊資源：

```php
use AdvSorcer\FilamentScheduleUI\Filament\Resources\ScheduledTasks\ScheduledTaskResource;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... 其他配置
        ->resources([
            ScheduledTaskResource::class,
        ]);
}
```

或者使用自動發現（如果資源在標準位置）：

```php
->discoverResources(
    in: base_path('vendor/advsorcer/filament-schedule-ui/src/Filament/Resources'),
    for: 'AdvSorcer\\FilamentScheduleUI\\Filament\\Resources'
)
```

### 6. 配置語言（可選）

在 `.env` 中設定語言：

```
FILAMENT_SCHEDULE_UI_LOCALE=zh_TW
# 或
FILAMENT_SCHEDULE_UI_LOCALE=en
```

預設為 `zh_TW`（繁體中文）。

## 功能特色

### 📋 排程管理
- **自動同步**
- **排程列表**
- **啟用/停用**
- **立即執行**
- **完整記錄**
- **執行狀態**
- **執行輸出**
- **執行時長**
- **錯誤追蹤**


## 使用方式

### 1. 首次設定

### 範例

在 `routes/console.php` 中定義排程：

```php
// 測試排程：每十分鐘執行一次
schedule_command('say:good-evening')
    ->everyTenMinutes()
    ->description('每十分鐘說晚安');
```

登入後台後，首次使用時需要將程式碼中的排程同步到資料庫：

1. 在 Filament 後台進入「排程任務」頁面
2. 點擊右上角的「同步排程」按鈕
3. 系統會自動掃描並同步所有排程任務


### ⚠️ 重要提醒

如果您建立了新的 Artisan 命令（Command），記得要在 `routes/console.php` 中使用 `schedule_command` 註冊排程，然後在後台執行「同步排程」才會出現在 UI 中。





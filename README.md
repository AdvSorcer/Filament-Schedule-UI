# Filament Schedule UI

一個基於 Filament 的 Laravel 排程管理工具，讓您可以在後台介面中輕鬆管理和監控所有排程任務。

![排程列表](docs/images/ui-1.png)

執行訊息

![執行訊息](docs/images/ui-2.png)

## 系統需求

- PHP >= 8.2
- Laravel >= 12.0
- Composer

## 安裝步驟

### 1. 安裝依賴

```bash
composer install
```

### 2. 環境配置

複製環境變數檔案並設定：

```bash
cp .env.example .env
php artisan key:generate
```

### 3. 資料庫設定

設定資料庫連線（在 `.env` 中），然後執行遷移：

```bash
php artisan migrate
```

### 4. 建立 Filament 管理員

建立第一個管理員帳號以登入後台：

```bash
php artisan make:filament-user
```

依照提示輸入姓名、電子郵件和密碼。

### 5. 存取後台

啟動應用程式後，前往：

```
http://your-domain/admin
```

使用剛才建立的管理員帳號登入。

## 功能特色

### 📋 排程管理
- **自動同步**：自動從程式碼中同步所有排程任務到資料庫
- **排程列表**：清晰顯示所有排程任務，包含名稱、命令、執行時間等資訊
- **啟用/停用**：一鍵切換排程的啟用狀態
- **立即執行**：手動觸發排程執行，無需等待排程時間
- **完整記錄**：記錄每次排程執行的詳細資訊
- **執行狀態**：顯示執行成功、失敗、跳過等狀態
- **執行輸出**：查看完整的命令執行輸出
- **執行時長**：記錄每次執行的耗時
- **錯誤追蹤**：記錄執行失敗時的錯誤訊息


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





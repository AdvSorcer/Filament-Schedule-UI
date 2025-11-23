# Filament Schedule UI

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4.svg)](https://www.php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Tests](https://github.com/AdvSorcer/Filament-Schedule-UI/actions/workflows/tests.yml/badge.svg)](https://github.com/AdvSorcer/Filament-Schedule-UI/actions)

English | [繁體中文](README.md)

A Laravel schedule management tool based on Filament that allows you to easily manage and monitor all scheduled tasks in the admin interface.

![Schedule List](docs/images/ui-1.png)

Execution Messages

![Execution Messages](docs/images/ui-2.png)


## System Requirements

- PHP >= 8.3
- Laravel >= 12.0
- Composer

## Installation Steps

### 1. Install Dependencies

```bash
composer install
```

### 2. Environment Configuration

Copy the environment variables file and configure:

```bash
cp .env.example .env
php artisan key:generate
```

#### Language: Supports Chinese and English Bilingual Interface

Configure in ENV
```
FILAMENT_SCHEDULE_UI_LOCALE=zh_TW
FILAMENT_SCHEDULE_UI_LOCALE=en
```

### 3. Database Setup

Configure the database connection (in `.env`), then run migrations:

```bash
php artisan migrate
```

### 4. Create Filament Admin

Create the first admin account to log in to the admin panel:

```bash
php artisan make:filament-user
```

Follow the prompts to enter your name, email, and password.

### 5. Access Admin Panel

After starting the application, navigate to:

```
php artisan serve
```

```
http://your-domain/admin
or
http://127.0.0.1:8000/admin
```

Log in with the admin account you just created.

## Features

### 📋 Schedule Management
- **Auto Sync**
- **Schedule List**
- **Enable/Disable**
- **Run Now**
- **Complete Logging**
- **Execution Status**
- **Execution Output**
- **Execution Duration**
- **Error Tracking**


## Usage

### 1. Initial Setup

### Example

Define schedules in `routes/console.php`:

```php
// Test schedule: runs every ten minutes
schedule_command('say:good-evening')
    ->everyTenMinutes()
    ->description('Say good evening every ten minutes');
```

After logging into the admin panel, you need to sync schedules from code to the database for the first time:

1. Go to the "Scheduled Tasks" page in the Filament admin panel
2. Click the "Sync Schedules" button in the top right corner
3. The system will automatically scan and sync all scheduled tasks


### ⚠️ Important Reminder

If you create a new Artisan command, remember to register it in `routes/console.php` using `schedule_command`, then execute "Sync Schedules" in the admin panel for it to appear in the UI.




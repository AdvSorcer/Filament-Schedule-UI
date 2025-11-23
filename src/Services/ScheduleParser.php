<?php

namespace AdvSorcer\FilamentScheduleUI\Services;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;

class ScheduleParser
{
    public function __construct() {}

    /**
     * 獲取 Schedule 實例
     */
    protected function getSchedule(): Schedule
    {
        return app(Schedule::class);
    }

    /**
     * 獲取所有排程並轉換為陣列格式
     */
    public function parse(): Collection
    {
        $schedule = $this->getSchedule();
        $events = $schedule->events();

        return collect($events)->map(function (Event $event) {
            return $this->parseEvent($event);
        });
    }

    /**
     * 解析單個排程事件
     */
    protected function parseEvent(Event $event): array
    {
        $command = $this->extractCommand($event);
        $commandType = $this->extractCommandType($event);
        $expression = $event->expression;
        $timezone = $this->extractTimezone($event);
        $metadata = $this->extractMetadata($event);

        $name = $this->generateName($command, $commandType, $expression, $metadata);

        return [
            'name' => $name,
            'description' => $this->extractDescription($event),
            'command' => $command,
            'command_type' => $commandType,
            'expression' => $expression,
            'timezone' => $timezone,
            'is_active' => true,
            'without_overlapping' => $this->hasWithoutOverlapping($event),
            'on_one_server' => $this->hasOnOneServer($event),
            'run_in_background' => $this->hasRunInBackground($event),
            'metadata' => $metadata,
        ];
    }

    /**
     * 提取命令字串
     */
    protected function extractCommand(Event $event): string
    {
        if ($event->command) {
            // 從完整的命令字串中提取純粹的 artisan 命令名稱
            return $this->extractArtisanCommandFromFullCommand($event->command);
        }

        if ($event->callback) {
            if (is_string($event->callback)) {
                return $event->callback;
            }

            if (is_array($event->callback)) {
                $class = is_object($event->callback[0]) ? get_class($event->callback[0]) : $event->callback[0];
                $method = $event->callback[1] ?? '__invoke';

                return $class.'@'.$method;
            }

            if (is_object($event->callback) && method_exists($event->callback, '__invoke')) {
                return get_class($event->callback).'@__invoke';
            }
        }

        return 'unknown';
    }

    /**
     * 提取命令類型
     */
    protected function extractCommandType(Event $event): string
    {
        if ($event->command) {
            return 'command';
        }

        if ($event->callback) {
            return 'call';
        }

        return 'exec';
    }

    /**
     * 提取時區
     */
    protected function extractTimezone(Event $event): ?string
    {
        try {
            $timezone = $event->timezone;
            if ($timezone) {
                return $timezone;
            }
        } catch (\Exception $e) {
            // 忽略錯誤
        }

        return null;
    }

    /**
     * 提取描述
     */
    protected function extractDescription(Event $event): ?string
    {
        try {
            $description = $event->description;
            if ($description) {
                return $description;
            }
        } catch (\Exception $e) {
            // 忽略錯誤
        }

        return null;
    }

    /**
     * 提取元數據（環境限制、條件等）
     */
    protected function extractMetadata(Event $event): array
    {
        $metadata = [];

        try {
            // 提取環境限制
            if (property_exists($event, 'environments') && $event->environments) {
                $metadata['environments'] = $event->environments;
            }

            // 提取其他條件（如果有）
            // 這裡可以根據需要擴展
        } catch (\Exception $e) {
            // 忽略錯誤
        }

        return $metadata;
    }

    /**
     * 檢查是否有 withoutOverlapping
     */
    protected function hasWithoutOverlapping(Event $event): bool
    {
        try {
            return property_exists($event, 'withoutOverlapping') && $event->withoutOverlapping;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 檢查是否有 onOneServer
     */
    protected function hasOnOneServer(Event $event): bool
    {
        try {
            return property_exists($event, 'onOneServer') && $event->onOneServer;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 檢查是否在背景執行
     */
    protected function hasRunInBackground(Event $event): bool
    {
        try {
            return property_exists($event, 'runInBackground') && $event->runInBackground;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 生成唯一名稱
     */
    protected function generateName(string $command, string $commandType, string $expression, array $metadata): string
    {
        $parts = [$commandType, $command, $expression];
        if (! empty($metadata)) {
            $parts[] = json_encode($metadata, JSON_SORT_KEYS);
        }

        return md5(implode('|', $parts));
    }

    /**
     * 從完整的命令字串中提取純粹的 artisan 命令名稱
     * 例如: "'/path/to/php' 'artisan' say:hi" -> "say:hi"
     */
    protected function extractArtisanCommandFromFullCommand(string $command): string
    {
        // 移除引號並分割命令
        $command = trim($command, "'\"");

        // 如果命令包含 'artisan'，提取 artisan 後面的部分
        if (preg_match("/['\"]artisan['\"]\s+(['\"]?)([^\s'\"]+)\\1/", $command, $matches)) {
            return $matches[2];
        }

        // 如果命令以 "artisan " 開頭，提取後面的部分
        if (preg_match("/artisan\s+(['\"]?)([^\s'\"]+)\\1/", $command, $matches)) {
            return $matches[2];
        }

        // 如果已經是純命令名稱（不包含路徑），直接返回
        if (! str_contains($command, '/') && ! str_contains($command, 'artisan')) {
            return $command;
        }

        // 嘗試從命令中提取最後一個參數（通常是命令名稱）
        $parts = preg_split("/\s+/", $command);
        foreach (array_reverse($parts) as $part) {
            $part = trim($part, "'\"");
            if (! empty($part) && ! str_contains($part, '/') && ! str_contains($part, 'php')) {
                return $part;
            }
        }

        // 如果都無法解析，返回原命令
        return $command;
    }
}



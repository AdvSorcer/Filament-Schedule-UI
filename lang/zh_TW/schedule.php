<?php

return [
    // Table columns
    'name' => '名稱',
    'command' => '命令',
    'type' => '類型',
    'cron_expression' => 'Cron 表達式',
    'enabled' => '啟用',
    'next_run' => '下次執行',
    'last_run' => '最後執行',

    // Status
    'status' => '狀態',
    'active' => '啟用',
    'inactive' => '停用',
    'enabled_status' => '已啟用',
    'disabled_status' => '已停用',

    // Command types
    'command_type' => 'Command',
    'call_type' => 'Call',
    'exec_type' => 'Exec',

    // Actions
    'run_now' => '立即執行',
    'sync_schedules' => '同步排程',
    'enable' => '啟用',
    'disable' => '停用',

    // Notifications
    'operation_success' => '操作成功',
    'operation_failed' => '操作失敗',
    'task_enabled' => '排程「:name」已啟用',
    'task_disabled' => '排程「:name」已停用',
    'update_status_error' => '更新排程狀態時發生錯誤: :error',

    // Run Now Action
    'confirm_run' => '確認執行排程',
    'confirm_run_description' => '您確定要立即執行這個排程嗎？',
    'run_success' => '執行成功',
    'run_failed' => '執行失敗',
    'task_run_success' => '排程已成功執行',
    'task_run_failed' => '排程執行失敗，退出碼: :code',
    'run_error' => '執行錯誤',
    'run_error_message' => '執行排程時發生錯誤: :error',

    // Sync Action
    'confirm_sync' => '確認同步',
    'confirm_sync_description' => '這將從程式碼中同步所有排程任務到資料庫。',
    'sync_complete' => '同步完成',
    'sync_success' => '排程已成功同步',

    // Logs
    'started_at' => '開始時間',
    'finished_at' => '結束時間',
    'duration' => '執行時長',
    'seconds' => '秒',
    'exit_code' => '退出碼',
    'error_message' => '錯誤訊息',
    'output' => '執行輸出',
    'view_full_output' => '查看完整輸出',
    'log_detail' => '執行記錄 #:id',

    // Log status
    'running' => '執行中',
    'success' => '成功',
    'failed' => '失敗',
    'skipped' => '跳過',
];


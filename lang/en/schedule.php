<?php

return [
    // Table columns
    'name' => 'Name',
    'command' => 'Command',
    'type' => 'Type',
    'cron_expression' => 'Cron Expression',
    'enabled' => 'Enabled',
    'next_run' => 'Next Run',
    'last_run' => 'Last Run',

    // Status
    'status' => 'Status',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'enabled_status' => 'Enabled',
    'disabled_status' => 'Disabled',

    // Command types
    'command_type' => 'Command',
    'call_type' => 'Call',
    'exec_type' => 'Exec',

    // Actions
    'run_now' => 'Run Now',
    'sync_schedules' => 'Sync Schedules',
    'enable' => 'Enable',
    'disable' => 'Disable',

    // Notifications
    'operation_success' => 'Operation Successful',
    'operation_failed' => 'Operation Failed',
    'task_enabled' => 'Schedule ":name" has been enabled',
    'task_disabled' => 'Schedule ":name" has been disabled',
    'update_status_error' => 'Error updating schedule status: :error',

    // Run Now Action
    'confirm_run' => 'Confirm Run',
    'confirm_run_description' => 'Are you sure you want to run this schedule now?',
    'run_success' => 'Run Successful',
    'run_failed' => 'Run Failed',
    'task_run_success' => 'Schedule has been executed successfully',
    'task_run_failed' => 'Schedule execution failed, exit code: :code',
    'run_error' => 'Execution Error',
    'run_error_message' => 'Error executing schedule: :error',

    // Sync Action
    'confirm_sync' => 'Confirm Sync',
    'confirm_sync_description' => 'This will sync all schedule tasks from code to database.',
    'sync_complete' => 'Sync Complete',
    'sync_success' => 'Schedules have been synced successfully',

    // Logs
    'started_at' => 'Started At',
    'finished_at' => 'Finished At',
    'duration' => 'Duration',
    'seconds' => 'seconds',
    'exit_code' => 'Exit Code',
    'error_message' => 'Error Message',
    'output' => 'Output',
    'view_full_output' => 'View Full Output',
    'log_detail' => 'Execution Log #:id',

    // Log status
    'running' => 'Running',
    'success' => 'Success',
    'failed' => 'Failed',
    'skipped' => 'Skipped',
];

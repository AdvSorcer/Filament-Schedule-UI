<div class="space-y-4">
    <div>
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">執行狀態</h3>
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">狀態：</span>
                    <span class="font-medium">{{ $log->status->value }}</span>
                </div>
                <div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">退出碼：</span>
                    <span class="font-medium">{{ $log->exit_code ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">開始時間：</span>
                    <span class="font-medium">{{ $log->started_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">結束時間：</span>
                    <span class="font-medium">{{ $log->finished_at?->format('Y-m-d H:i:s') ?? '-' }}</span>
                </div>
                @if($log->duration)
                <div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">執行時長：</span>
                    <span class="font-medium">{{ number_format($log->duration / 1000, 2) }} 秒</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($log->output)
    <div>
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">執行輸出</h3>
        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg font-mono text-sm overflow-x-auto">
            <pre class="whitespace-pre-wrap">{{ $log->output }}</pre>
        </div>
    </div>
    @endif

    @if($log->error_message)
    <div>
        <h3 class="text-sm font-medium text-red-700 dark:text-red-300 mb-2">錯誤訊息</h3>
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 rounded-lg">
            <pre class="whitespace-pre-wrap text-sm text-red-800 dark:text-red-200">{{ $log->error_message }}</pre>
        </div>
    </div>
    @endif
</div>


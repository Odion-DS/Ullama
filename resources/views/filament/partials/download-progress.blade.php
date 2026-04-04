@if($this->downloadProgress)
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800" wire:poll.500ms="checkProgress">
        <div class="mb-2 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                    Downloading: {{ $this->downloadProgress['model'] }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $this->downloadProgress['status'] }}
                </p>
            </div>
            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                {{ $this->downloadProgress['percent'] }}%
            </div>
        </div>

        <div class="mb-2 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
            <div
                class="h-full rounded-full bg-primary-600 transition-all duration-300 dark:bg-primary-500"
                style="width: {{ $this->downloadProgress['percent'] }}%"
            ></div>
        </div>

        @if(isset($this->downloadProgress['completed']) && isset($this->downloadProgress['total']) && $this->downloadProgress['total'] > 0)
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ $this->formatBytes($this->downloadProgress['completed']) }} / {{ $this->formatBytes($this->downloadProgress['total']) }}
            </p>
        @endif
    </div>
@endif

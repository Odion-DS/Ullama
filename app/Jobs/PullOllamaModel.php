<?php

namespace App\Jobs;

use App\Facades\Ollama;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class PullOllamaModel implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $modelName,
        public string $progressKey
    ) {}

    public function handle(): void
    {
        Cache::put($this->progressKey, [
            'model' => $this->modelName,
            'status' => 'Starting download...',
            'percent' => 0,
            'completed' => 0,
            'total' => 0,
        ], now()->addHours(1));

        $result = Ollama::pullModel($this->modelName, function ($progress) {
            $percent = 0;
            if (isset($progress['completed']) && isset($progress['total']) && $progress['total'] > 0) {
                $percent = (int) (($progress['completed'] / $progress['total']) * 100);
            }

            Cache::put($this->progressKey, [
                'model' => $this->modelName,
                'status' => $progress['status'] ?? 'Downloading...',
                'percent' => $percent,
                'completed' => $progress['completed'] ?? 0,
                'total' => $progress['total'] ?? 0,
            ], now()->addHours(1));
        });

        // Mark as completed
        Cache::put($this->progressKey, [
            'model' => $this->modelName,
            'status' => $result['success'] ? 'Completed' : 'Failed',
            'percent' => $result['success'] ? 100 : 0,
            'completed' => 0,
            'total' => 0,
            'success' => $result['success'],
            'message' => $result['message'],
            'finished' => true,
        ], now()->addHours(1));
    }
}

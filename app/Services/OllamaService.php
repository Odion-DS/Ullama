<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OllamaService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('OLLAMA_BASE_URL', 'http://localhost:11434');
    }

    /**
     * Get all installed models from Ollama
     */
    public function getInstalledModels(): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/api/tags");

            if ($response->successful()) {
                $data = $response->json();
                return $data['models'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            \Log::error('Failed to fetch Ollama models: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Pull/download a model from Ollama library with progress callback
     * @param callable|null $progressCallback Function to call with progress updates
     * @return array ['success' => bool, 'message' => string]
     */
    public function pullModel(string $model, ?callable $progressCallback = null): array
    {
        try {
            $response = Http::timeout(3600)->withOptions(['stream' => true])->post("{$this->baseUrl}/api/pull", [
                'name' => $model,
                'stream' => true,
            ]);

            if (!$response->successful()) {
                $error = $response->json('error') ?? $response->body();

                // Make error messages user-friendly
                if (str_contains($error, 'file does not exist')) {
                    return ['success' => false, 'message' => "Model '{$model}' not found. Please check the model name."];
                }

                if (str_contains($error, 'connection refused')) {
                    return ['success' => false, 'message' => 'Cannot connect to Ollama. Make sure Ollama is running.'];
                }

                return ['success' => false, 'message' => $error];
            }

            // Process streaming response
            $body = $response->toPsrResponse()->getBody();
            while (!$body->eof()) {
                $line = $this->readLine($body);
                if (empty($line)) {
                    continue;
                }

                $data = json_decode($line, true);
                if ($data && $progressCallback) {
                    $progressCallback($data);
                }

                // Check for completion or error
                if (isset($data['error'])) {
                    return ['success' => false, 'message' => $data['error']];
                }
            }

            return ['success' => true, 'message' => 'Model downloaded successfully'];
        } catch (ConnectionException $e) {
            return ['success' => false, 'message' => 'Cannot connect to Ollama. Make sure Ollama is running.'];
        } catch (\Exception $e) {
            \Log::error('Failed to pull model: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Read a line from stream
     */
    private function readLine($stream): string
    {
        $line = '';
        while (!$stream->eof()) {
            $char = $stream->read(1);
            if ($char === "\n") {
                break;
            }
            $line .= $char;
        }
        return trim($line);
    }

    /**
     * Delete a model
     */
    public function deleteModel(string $model): bool
    {
        try {
            $response = Http::delete("{$this->baseUrl}/api/delete", [
                'name' => $model,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Failed to delete model: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Show model information
     */
    public function showModelInfo(string $model): ?array
    {
        try {
            $response = Http::post("{$this->baseUrl}/api/show", [
                'name' => $model,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Failed to get model info: ' . $e->getMessage());
            return null;
        }
    }
}

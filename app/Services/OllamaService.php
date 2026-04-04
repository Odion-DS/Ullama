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
     * Pull/download a model from Ollama library
     * @return array ['success' => bool, 'message' => string]
     */
    public function pullModel(string $model): array
    {
        try {
            $response = Http::timeout(3600)->post("{$this->baseUrl}/api/pull", [
                'name' => $model,
                'stream' => false,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Model downloaded successfully'];
            }

            // Parse error from response
            $error = $response->json('error') ?? $response->body();

            // Make error messages user-friendly
            if (str_contains($error, 'file does not exist')) {
                return ['success' => false, 'message' => "Model '{$model}' not found. Please check the model name."];
            }

            if (str_contains($error, 'connection refused')) {
                return ['success' => false, 'message' => 'Cannot connect to Ollama. Make sure Ollama is running.'];
            }

            return ['success' => false, 'message' => $error];
        } catch (ConnectionException $e) {
            return ['success' => false, 'message' => 'Cannot connect to Ollama. Make sure Ollama is running.'];
        } catch (\Exception $e) {
            \Log::error('Failed to pull model: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()];
        }
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

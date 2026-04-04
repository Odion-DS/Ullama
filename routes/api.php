<?php

use App\Enums\OllamaPermission;
use App\Facades\Ollama;
use App\Http\Middleware\CheckOllamaPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Helper function to create Ollama proxy routes


Route::post('generate', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth', CheckOllamaPermission::class . ':' . OllamaPermission::CAN_GENERATE_RESPONSE->name])
    ->name('ollama.generate');


Route::post('chat', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth', CheckOllamaPermission::class . ':' . OllamaPermission::CAN_GENERATE_CHAT_MESSAGE->name]
    )
    ->name('ollama.chat');

Route::post('embeddings', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth', CheckOllamaPermission::class . ':' . OllamaPermission::CAN_GENERATE_EMBEDDINGS->name])
    ->name('ollama.embeddings');


Route::get('tags', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth', CheckOllamaPermission::class . ':' . OllamaPermission::CAN_LIST_MODELS->name])
    ->name('ollama.tags');

Route::get('version', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth'])
    ->name('ollama.version');

Route::post('show', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth', CheckOllamaPermission::class . ':' . OllamaPermission::CAN_SHOW_MODEL_DETAIL->name])
    ->name('ollama.show');

Route::post('copy', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth', CheckOllamaPermission::class . ':' . OllamaPermission::CAN_COPY_MODEL->name])
    ->name('ollama.copy');

Route::post('pull', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth', CheckOllamaPermission::class . ':' . OllamaPermission::CAN_PULL_MODEL->name])
    ->name('ollama.pull');

Route::post('push', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth', CheckOllamaPermission::class . ':' . OllamaPermission::CAN_PUSH_MODEL->name])
    ->name('ollama.push');


Route::delete('delete', fn(Request $request) => Ollama::redirectRequest($request))
    ->middleware(['ollama.auth', CheckOllamaPermission::class . ':' . OllamaPermission::CAN_DELETE_MODEL->name])
    ->name('ollama.delete');

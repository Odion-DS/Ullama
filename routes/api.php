<?php

use App\Enums\OllamaPermission;
use App\Facades\Ollama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('generate', function (Request $request) {
    /** @var \App\Models\OllamaToken $ollamaToken */
    $ollamaToken = $request->user();

    if (!$ollamaToken->canApiCall(OllamaPermission::CAN_GENERATE_RESPONSE)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    return Ollama::redirectRequest($request);
})
    ->middleware('ollama.auth')
    ->name('ollama.generate');

<?php

namespace App\Filament\Resources\OllamaTokens\Pages;

use App\Filament\Resources\OllamaTokens\OllamaTokenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOllamaTokens extends ListRecords
{
    protected static string $resource = OllamaTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

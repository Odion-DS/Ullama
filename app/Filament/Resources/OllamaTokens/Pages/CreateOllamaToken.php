<?php

namespace App\Filament\Resources\OllamaTokens\Pages;

use App\Filament\Resources\OllamaTokens\OllamaTokenResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOllamaToken extends CreateRecord
{
    protected static string $resource = OllamaTokenResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->plainToken) {
            session()->flash('created_token', $this->record->plainToken);
            $this->redirect($this->getResource()::getUrl('created', ['record' => $this->record]));
        }
    }
}

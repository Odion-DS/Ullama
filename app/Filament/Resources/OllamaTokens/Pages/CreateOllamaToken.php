<?php

namespace App\Filament\Resources\OllamaTokens\Pages;

use App\Filament\Resources\OllamaTokens\OllamaTokenResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOllamaToken extends CreateRecord
{
    protected static string $resource = OllamaTokenResource::class;

    protected function getRedirectUrl(): string
    {
        if ($this->getRecord()->plainToken) {
            session()->flash('created_token', $this->record->plainToken);
            return $this->getResource()::getUrl('created');
        }

        return $this->getResource()::getUrl('index');
    }


}

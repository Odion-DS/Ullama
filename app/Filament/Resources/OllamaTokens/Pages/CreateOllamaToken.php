<?php

namespace App\Filament\Resources\OllamaTokens\Pages;

use App\Filament\Resources\OllamaTokens\OllamaTokenResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOllamaToken extends CreateRecord
{
    protected static string $resource = OllamaTokenResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        if ($this->record->plainToken) {
            session()->flash('created_token', $this->record->plainToken);
            return $this->getResource()::getUrl('created', ['record' => $this->record]);
        }

        return parent::getRedirectUrl();
    }


}

<?php

namespace App\Filament\Resources\OllamaTokens\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OllamaTokenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                DatePicker::make('expires_at')
                    ->after(now())
                    ->required(),
            ]);
    }
}

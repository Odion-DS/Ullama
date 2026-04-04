<?php

namespace App\Filament\Resources\OllamaTokens\Schemas;

use App\Enums\OllamaPermission;
use Filament\Forms\Components\CheckboxList;
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

                CheckboxList::make('capabilities')
                    ->label('Capabilities')
                    ->options(collect(OllamaPermission::cases())
                        ->mapWithKeys(fn($permission) => [$permission->value => $permission->label()])
                        ->all())
                    ->columns(2)
                    ->bulkToggleable(),
            ]);
    }
}

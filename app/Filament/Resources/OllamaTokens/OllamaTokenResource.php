<?php

namespace App\Filament\Resources\OllamaTokens;

use App\Filament\Resources\OllamaTokens\Pages\CreateOllamaToken;
use App\Filament\Resources\OllamaTokens\Pages\EditOllamaToken;
use App\Filament\Resources\OllamaTokens\Pages\ListOllamaTokens;
use App\Filament\Resources\OllamaTokens\Schemas\OllamaTokenForm;
use App\Filament\Resources\OllamaTokens\Tables\OllamaTokensTable;
use App\Models\OllamaToken;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OllamaTokenResource extends Resource
{
    protected static ?string $model = OllamaToken::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OllamaTokenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OllamaTokensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOllamaTokens::route('/'),
            'create' => CreateOllamaToken::route('/create'),
        ];
    }
}

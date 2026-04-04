<?php

namespace App\Filament\Resources\OllamaTokens\Schemas;

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
                    ->options([
                        'can_generate_response' => 'Can Generate Response',
                        'can_generate_chat_message' => 'Can Generate Chat Message',
                        'can_generate_embeddings' => 'Can Generate Embeddings',
                        'can_list_models' => 'Can List Models',
                        'can_show_model_detail' => 'Can Show Model Detail',
                        'can_create_model' => 'Can Create Model',
                        'can_copy_model' => 'Can Copy Model',
                        'can_pull_model' => 'Can Pull Model',
                        'can_push_model' => 'Can Push Model',
                        'can_delete_model' => 'Can Delete Model',
                    ])
                    ->columns(2)
                    ->bulkToggleable(),
            ]);
    }
}

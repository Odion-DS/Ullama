<?php

namespace App\Filament\Resources\OllamaTokens\Tables;

use App\Models\OllamaToken;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OllamaTokensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('expires_at')
                    ->label('Expires at')
                    ->sortable()
                    ->date(),

                IconColumn::make('is_expired')
                    ->label('Expired')
                    ->alignCenter()
                    ->state(fn($record) => $record->isExpired())
                    ->boolean(),

                TextColumn::make('capabilities')
                    ->label('Capabilities')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'can_generate_response' => 'Generate Response',
                        'can_generate_chat_message' => 'Chat Message',
                        'can_generate_embeddings' => 'Embeddings',
                        'can_list_models' => 'List Models',
                        'can_show_model_detail' => 'Model Detail',
                        'can_create_model' => 'Create Model',
                        'can_copy_model' => 'Copy Model',
                        'can_pull_model' => 'Pull Model',
                        'can_push_model' => 'Push Model',
                        'can_delete_model' => 'Delete Model',
                        default => $state,
                    })
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('show_token')
                    ->fillForm(fn(OllamaToken $record): array => ['token' => $record->token])
                    ->modalSubmitAction(fn(Action $action) => $action->hidden())
                    ->label('Token')
                    ->icon('heroicon-o-eye')
                    ->iconButton()
                    ->schema([
                        TextInput::make('token')
                            ->hiddenLabel()
                            ->disabled()
                            ->copyable(),
                    ]),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

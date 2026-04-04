<?php

namespace App\Filament\Pages;

use App\Facades\Ollama;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class InstalledModelPage extends Page implements HasTable
{
    protected static ?string $title = 'Installed Models';
    protected static ?string $slug = 'installed-models';

    use InteractsWithTable {
        makeTable as makeBaseTable;
    }


    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }

    protected function makeTable(): Table
    {
        $table = $this->makeBaseTable();

        $table->records(fn() => Ollama::getInstalledModels())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('model'),
                TextColumn::make('size')
                    ->formatStateUsing(fn($state) => number_format($state / 1024 / 1024 / 1024, 2) . ' GB')
            ])
            ->recordActions([
                Action::make('delete_model')
                    ->icon('heroicon-o-trash')
                    ->label('Delete')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function (array $record) {
                        Ollama::deleteModel($record['name']);
                        $this->refresh();
                    })
            ])
            ->headerActions([
                Action::make('add_model')
                    ->color('success')
                    ->icon(Heroicon::PlusCircle)
                    ->label('Download Model')
                    ->form([
                        TextInput::make('model_name')
                            ->label('Model Name')
                            ->placeholder('e.g., llama2, mistral, codellama')
                            ->required()
                            ->helperText('Enter the name of the model from Ollama library')
                    ])
                    ->action(function (array $data, Action $action) {
                        $result = Ollama::pullModel($data['model_name']);

                        if ($result['success']) {
                            $action->success();
                            $action->sendSuccessNotification();
                            $this->refresh();
                        } else {
                            $action->failure();
                            $action->failureNotificationTitle($result['message']);
                            $action->sendFailureNotification();
                        }
                    })
                    ->successNotificationTitle('Model downloaded successfully')
                    ->failureNotificationTitle('Failed to download model')
            ]);

        return $table;
    }
}

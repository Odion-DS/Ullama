<?php

namespace App\Filament\Pages;

use App\Facades\Ollama;
use App\Jobs\PullOllamaModel;
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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class InstalledModelPage extends Page implements HasTable
{
    protected static ?string $title = 'Installed Models';
    protected static ?string $slug = 'installed-models';

    public ?array $downloadProgress = null;
    public bool $isDownloading = false;
    public ?string $progressKey = null;

    use InteractsWithTable {
        makeTable as makeBaseTable;
    }

    public function mount(): void
    {
        // Check if there's an ongoing download
        if ($this->progressKey) {
            $this->checkProgress();
        }
    }


    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\View::make('filament.partials.download-progress')
                    ->visible(fn() => $this->isDownloading && $this->downloadProgress !== null),
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
                        // Generate unique progress key
                        $this->progressKey = 'model-download-' . Str::random(16);
                        $this->isDownloading = true;

                        // Initialize progress in cache
                        Cache::put($this->progressKey, [
                            'model' => $data['model_name'],
                            'status' => 'Starting download...',
                            'percent' => 0,
                            'completed' => 0,
                            'total' => 0,
                        ], now()->addHours(1));

                        // Set initial progress state
                        $this->downloadProgress = [
                            'model' => $data['model_name'],
                            'status' => 'Starting download...',
                            'percent' => 0,
                            'completed' => 0,
                            'total' => 0,
                        ];

                        // Dispatch the job
                        PullOllamaModel::dispatch($data['model_name'], $this->progressKey);

                        $action->success();
                        $action->successNotificationTitle('Download started');
                        $action->sendSuccessNotification();
                    })
                    ->successNotificationTitle('Model downloaded successfully')
                    ->failureNotificationTitle('Failed to download model')
            ]);

        return $table;
    }

    public function checkProgress(): void
    {
        if (!$this->progressKey) {
            return;
        }

        $progress = Cache::get($this->progressKey);

        if ($progress) {
            $this->downloadProgress = $progress;

            // Check if download is finished
            if (isset($progress['finished']) && $progress['finished']) {
                $this->isDownloading = false;

                if ($progress['success']) {
                    \Filament\Notifications\Notification::make()
                        ->title('Model downloaded successfully')
                        ->success()
                        ->send();
                } else {
                    \Filament\Notifications\Notification::make()
                        ->title('Download failed')
                        ->body($progress['message'] ?? 'Unknown error')
                        ->danger()
                        ->send();
                }

                // Clean up
                Cache::forget($this->progressKey);
                $this->progressKey = null;
                $this->downloadProgress = null;
            }
        }
    }

    private function calculatePercent(array $progress): int
    {
        if (isset($progress['completed']) && isset($progress['total']) && $progress['total'] > 0) {
            return (int) (($progress['completed'] / $progress['total']) * 100);
        }
        return 0;
    }

    public function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

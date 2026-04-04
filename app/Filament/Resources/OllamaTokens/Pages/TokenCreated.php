<?php

namespace App\Filament\Resources\OllamaTokens\Pages;

use App\Filament\Resources\OllamaTokens\OllamaTokenResource;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class TokenCreated extends Page
{
    protected static string $resource = OllamaTokenResource::class;

    public ?string $token = null;

    public function getTitle(): string|Htmlable
    {
        return 'Created Token';
    }

    protected function getViewData(): array
    {
        return [
            'token' => $this->token,
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()
                ->schema([
                    TextEntry::make('info')
                        ->hiddenLabel()
                        ->state(
                            'Your token has been created. Save it somewhere safe, you won\'t be able to see it again.'
                        ),

                    TextInput::make('token')
                        ->hiddenLabel()
                        ->copyable()
                        ->disabled(),
                ])
        ]);
    }

    public function mount(): void
    {
        $this->token = session()->pull('created_token');

        if (!$this->token) {
            $this->redirect($this->getResource()::getUrl('index'));
        }
    }


}

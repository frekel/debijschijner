<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pagina Informatie')
                    ->schema([
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(table: 'pages', column: 'slug', ignoreRecord: true)
                            ->helperText('Unieke URL-vriendelijke naam (bijv. "over-mij")'),
                        TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->helperText('Weergavetitel van de pagina'),
                        Toggle::make('is_published')
                            ->label('Gepubliceerd')
                            ->default(true)
                            ->helperText('Pagina is zichtbaar voor bezoekers'),
                    ]),
                Section::make('HTML Inhoud')
                    ->schema([
                        Textarea::make('html')
                            ->label('HTML')
                            ->required()
                            ->rows(20)
                            ->columnSpanFull()
                            ->helperText('Volledige HTML-inhoud van de pagina'),
                    ]),
            ]);
    }
}

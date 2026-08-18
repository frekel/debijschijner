<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
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
                        Select::make('template')
                            ->label('Page template')
                            ->options([
                                'default' => 'Default',
                                'homepage' => 'Homepage',
                                'full_screen' => 'Full screen',
                                'form' => 'Form',
                            ])
                            ->default('default')
                            ->required()
                            ->helperText('Kies welke layout deze pagina gebruikt voor CMS-blokken.'),
                        Toggle::make('is_published')
                            ->label('Gepubliceerd')
                            ->default(true)
                            ->helperText('Pagina is zichtbaar voor bezoekers'),
                        Toggle::make('show_in_menu')
                            ->label('Tonen in menu')
                            ->default(true)
                            ->helperText('Bepaalt of deze pagina in het hoofdmenu zichtbaar is.'),
                    ]),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(70),
                        Textarea::make('meta_description')
                            ->label('Meta description')
                            ->rows(3)
                            ->maxLength(170),
                        FileUpload::make('og_image')
                            ->label('OG afbeelding')
                            ->image()
                            ->disk('public')
                            ->directory('seo'),
                        TextInput::make('canonical_url')
                            ->label('Canonical URL')
                            ->url(),
                    ]),
                Section::make('Form template')
                    ->visible(fn (callable $get): bool => ($get('template') ?? 'default') === 'form')
                    ->schema([
                        TextInput::make('form_title')
                            ->label('Form titel')
                            ->default('Neem contact op'),
                    ]),
                Section::make('CMS Inhoud (Nieuw)')
                    ->schema([
                        Builder::make('content_blocks')
                            ->label('Inhoudsblokken')
                            ->blocks([
                                Block::make('rich_text')
                                    ->label('Tekst')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->label('Kop'),
                                        RichEditor::make('body')
                                            ->label('Inhoud')
                                            ->visible(fn (callable $get): bool => ($get('editor_mode') ?? 'wysiwyg') === 'wysiwyg')
                                            ->required(fn (callable $get): bool => ($get('editor_mode') ?? 'wysiwyg') === 'wysiwyg'),
                                        Textarea::make('body_html')
                                            ->label('HTML code')
                                            ->rows(12)
                                            ->visible(fn (callable $get): bool => ($get('editor_mode') ?? 'wysiwyg') === 'html')
                                            ->required(fn (callable $get): bool => ($get('editor_mode') ?? 'wysiwyg') === 'html')
                                            ->helperText('Plak hier HTML als je geen WYSIWYG wilt gebruiken.'),
                                        ToggleButtons::make('editor_mode')
                                            ->label('Editor modus')
                                            ->options([
                                                'wysiwyg' => 'WYSIWYG',
                                                'html' => 'HTML code',
                                            ])
                                            ->default('wysiwyg')
                                            ->inline()
                                            ->live(),
                                    ]),
                                Block::make('image_text')
                                    ->label('Afbeelding + Tekst')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Afbeelding')
                                            ->image()
                                            ->disk('public_uploads')
                                            ->directory(fn (): string => now()->format('Y/m'))
                                            ->preserveFilenames()
                                            ->required(),
                                        TextInput::make('alt')
                                            ->label('Alt tekst')
                                            ->required(),
                                        TextInput::make('heading')
                                            ->label('Kop'),
                                        RichEditor::make('body')
                                            ->label('Inhoud')
                                            ->visible(fn (callable $get): bool => ($get('editor_mode') ?? 'wysiwyg') === 'wysiwyg'),
                                        Textarea::make('body_html')
                                            ->label('HTML code')
                                            ->rows(12)
                                            ->visible(fn (callable $get): bool => ($get('editor_mode') ?? 'wysiwyg') === 'html')
                                            ->helperText('Plak hier HTML als je geen WYSIWYG wilt gebruiken.'),
                                        ToggleButtons::make('editor_mode')
                                            ->label('Editor modus')
                                            ->options([
                                                'wysiwyg' => 'WYSIWYG',
                                                'html' => 'HTML code',
                                            ])
                                            ->default('wysiwyg')
                                            ->inline()
                                            ->live(),
                                    ]),
                                Block::make('quote')
                                    ->label('Quote')
                                    ->schema([
                                        Textarea::make('quote_text')
                                            ->label('Quote tekst')
                                            ->rows(4)
                                            ->required()
                                            ->helperText('Bijv: De beste leraren zijn degene die je laten zien waar te kijken, maar niet vertellen wat te zien.'),
                                        TextInput::make('quote_author')
                                            ->label('Auteur')
                                            ->required()
                                            ->helperText('Bijv: Alexandra Trenfor'),
                                    ]),
                                Block::make('homepage_post')
                                    ->label('Homepage tekst')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Titel')
                                            ->required(),
                                        Textarea::make('text')
                                            ->label('Tekst')
                                            ->rows(8)
                                            ->required(),
                                    ]),
                                Block::make('process_post')
                                    ->label('Process post')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Title')
                                            ->required(),
                                        TextInput::make('time')
                                            ->label('Time')
                                            ->helperText('Bijv: 45 min, 1 uur, 60 - 90 min (optioneel)'),
                                        Textarea::make('text')
                                            ->label('Text')
                                            ->rows(6)
                                            ->required(),
                                    ]),
                                Block::make('prices')
                                    ->label('Prices')
                                    ->schema([
                                        TextInput::make('price')
                                            ->label('Prijs')
                                            ->required(),
                                        TextInput::make('title')
                                            ->label('Titel')
                                            ->required(),
                                        Textarea::make('text')
                                            ->label('Text')
                                            ->rows(6)
                                            ->required(),
                                    ]),
                                Block::make('reviews')
                                    ->label('Reviews')
                                    ->schema([]),
                                Block::make('publications')
                                    ->label('Publicaties')
                                    ->schema([]),
                                Block::make('contact_form')
                                    ->label('Contact formulier')
                                    ->schema([]),
                                Block::make('apply_form')
                                    ->label('Aanvraagformulier')
                                    ->schema([]),
                            ])
                            ->addActionLabel('Blok toevoegen')
                            ->collapsible()
                            ->columnSpanFull()
                                ->helperText('Wanneer je hier blokken toevoegt, gebruikt de frontend deze CMS-opmaak.'),
                    ]),
            ]);
    }
}

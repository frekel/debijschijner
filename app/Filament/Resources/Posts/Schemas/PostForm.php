<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Instellingen')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'post' => 'Post',
                                'reviewer' => 'Review',
                                'publicatie' => 'Publicatie',
                            ])
                            ->required()
                            ->default('post')
                            ->live(),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Voor reviews en publicaties: de URL-slug van het item')
                            ->visible(fn (callable $get): bool => in_array(($get('type') ?? 'post'), ['reviewer', 'publicatie'], true))
                            ->required(fn (callable $get): bool => in_array(($get('type') ?? 'post'), ['reviewer', 'publicatie'], true)),
                        TextInput::make('sort_order')
                            ->label('Sorteervolgorde')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_published')
                            ->label('Gepubliceerd')
                            ->default(true),
                    ]),
                Section::make('Inhoud')
                    ->schema([
                        Textarea::make('title')
                            ->label('Titel/Kop')
                            ->rows(3)
                            ->columnSpanFull()
                            ->required(),
                        RichEditor::make('text')
                            ->label('Inhoud/Tekst')
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $set('text_html', $state ?? '');
                            })
                            ->visible(fn (callable $get): bool => ($get('editor_mode') ?? 'wysiwyg') === 'wysiwyg')
                            ->required(fn (callable $get): bool => ($get('editor_mode') ?? 'wysiwyg') === 'wysiwyg'),
                        Textarea::make('text_html')
                            ->label('HTML code')
                            ->rows(12)
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->afterStateHydrated(function (Textarea $component, Get $get): void {
                                $component->state((string) ($get('text') ?? ''));
                            })
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $set('text', $state ?? '');
                            })
                            ->dehydrated(false)
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
                Section::make('Post Informatie')
                    ->schema([
                        TextInput::make('name')
                            ->label('Naam')
                            ->helperText('Voor reviews: naam van de reviewer.')
                            ->visible(fn (callable $get): bool => ($get('type') ?? 'post') === 'reviewer')
                            ->required(fn (callable $get): bool => ($get('type') ?? 'post') === 'reviewer'),
                        Textarea::make('button_text')
                            ->label('Button tekst')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Voor reviews: tekst onder de naam reviewer. Voor publicaties: extra tekstveld voor de kaart.')
                            ->visible(fn (callable $get): bool => in_array(($get('type') ?? 'post'), ['reviewer', 'publicatie'], true)),
                        FileUpload::make('image')
                            ->label('Afbeelding')
                            ->image()
                            ->disk('public_uploads')
                            ->directory(fn (): string => now()->format('Y/m'))
                            ->preserveFilenames()
                            ->visible(fn (callable $get): bool => in_array(($get('type') ?? 'post'), ['reviewer', 'publicatie'], true))
                            ->required(fn (callable $get): bool => in_array(($get('type') ?? 'post'), ['reviewer', 'publicatie'], true)),
                    ]),
            ]);
    }
}

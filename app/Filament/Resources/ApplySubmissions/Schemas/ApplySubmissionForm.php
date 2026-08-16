<?php

namespace App\Filament\Resources\ApplySubmissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ApplySubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('Voornaam')
                    ->disabled()
                    ->required(),
                TextInput::make('last_name')
                    ->label('Achternaam')
                    ->disabled()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->disabled()
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('Telefoon')
                    ->disabled()
                    ->tel()
                    ->required(),
                TextInput::make('trajectory')
                    ->label('Traject')
                    ->disabled()
                    ->required(),
                Textarea::make('message')
                    ->label('Bericht')
                    ->disabled()
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->label('IP Adres')
                    ->disabled()
                    ->default(null),
                Toggle::make('contacted')
                    ->label('Contacted')
                    ->disabled(),
                Textarea::make('user_agent')
                    ->label('User Agent')
                    ->disabled()
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}

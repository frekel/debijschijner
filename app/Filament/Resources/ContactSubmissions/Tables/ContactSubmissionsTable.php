<?php

namespace App\Filament\Resources\ContactSubmissions\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('first_name')
                    ->label('Voornaam')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('Achternaam')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->url(fn ($record) => "mailto:{$record->email}")
                    ->openUrlInNewTab()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefoon')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Ingediend')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('contacted')
                    ->label('Contacted')
                    ->boolean()
                    ->sortable()
                    ->action(function ($record): void {
                        $record->update(['contacted' => ! $record->contacted]);
                    })
                    ->tooltip(fn ($record): string => $record->contacted ? 'Klik om op niet gecontacteerd te zetten' : 'Klik om op gecontacteerd te zetten'),
            ])
            ->filters([
                //
            ])
            ->toolbarActions([])
            ->recordActions([]);
    }
}

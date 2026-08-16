<?php

namespace App\Filament\Resources\ContactSubmissions\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
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
                TextColumn::make('ip_address')
                    ->label('IP Adres')
                    ->searchable(),
                IconColumn::make('contacted')
                    ->label('Contacted')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ingediend')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->toolbarActions([])
            ->recordActions([
                Action::make('toggleContacted')
                    ->label(fn ($record): string => $record->contacted ? 'Markeer als niet gecontacteerd' : 'Markeer als gecontacteerd')
                    ->color(fn ($record): string => $record->contacted ? 'gray' : 'success')
                    ->icon(fn ($record): string => $record->contacted ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $record->update(['contacted' => ! $record->contacted]);
                    }),
            ]);
    }
}

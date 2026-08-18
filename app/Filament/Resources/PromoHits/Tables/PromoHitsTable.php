<?php

namespace App\Filament\Resources\PromoHits\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PromoHitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Bezocht')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable(),
                TextColumn::make('page_title')
                    ->label('Pagina titel')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable(),
                TextColumn::make('referer')
                    ->label('Referer')
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([])
            ->toolbarActions([])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
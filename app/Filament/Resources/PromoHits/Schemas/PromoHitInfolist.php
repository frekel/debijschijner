<?php

namespace App\Filament\Resources\PromoHits\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PromoHitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Bezocht')
                    ->dateTime('d-m-Y H:i:s'),
                TextEntry::make('page_slug')
                    ->label('Pagina slug')
                    ->placeholder('-'),
                TextEntry::make('page_title')
                    ->label('Pagina titel')
                    ->placeholder('-'),
                TextEntry::make('redirect_target')
                    ->label('Redirect target')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('full_url')
                    ->label('Volledige URL')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('method')
                    ->label('Method')
                    ->placeholder('-'),
                TextEntry::make('host')
                    ->label('Host')
                    ->placeholder('-'),
                TextEntry::make('ip_address')
                    ->label('IP adres')
                    ->placeholder('-'),
                TextEntry::make('referer')
                    ->label('Referer')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('accept_language')
                    ->label('Accept-Language')
                    ->placeholder('-'),
                TextEntry::make('user_agent')
                    ->label('User agent')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('query_params')
                    ->label('Query params')
                    ->formatStateUsing(fn ($state): string => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '' : '-')
                    ->columnSpanFull(),
                TextEntry::make('headers')
                    ->label('Headers')
                    ->formatStateUsing(fn ($state): string => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '' : '-')
                    ->columnSpanFull(),
            ]);
    }
}
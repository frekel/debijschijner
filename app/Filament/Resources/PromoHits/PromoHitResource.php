<?php

namespace App\Filament\Resources\PromoHits;

use App\Filament\Resources\PromoHits\Pages\ViewPromoHit;
use App\Filament\Resources\PromoHits\Pages\ListPromoHits;
use App\Filament\Resources\PromoHits\Schemas\PromoHitInfolist;
use App\Filament\Resources\PromoHits\Tables\PromoHitsTable;
use App\Models\PromoHit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PromoHitResource extends Resource
{
    protected static ?string $model = PromoHit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $slug = 'promo-hits';

    protected static ?string $recordTitleAttribute = 'full_url';

    protected static ?string $navigationLabel = 'Promo hits';

    protected static ?string $modelLabel = 'Promo hit';

    protected static ?string $pluralModelLabel = 'Promo hits';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return PromoHitsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PromoHitInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromoHits::route('/'),
            'view' => ViewPromoHit::route('/{record}'),
        ];
    }
}
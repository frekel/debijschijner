<?php

namespace App\Filament\Resources\PromoHits\Pages;

use App\Filament\Resources\PromoHits\PromoHitResource;
use Filament\Resources\Pages\ListRecords;

class ListPromoHits extends ListRecords
{
    protected static string $resource = PromoHitResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
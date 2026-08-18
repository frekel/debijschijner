<?php

namespace App\Filament\Resources\PromoHits\Pages;

use App\Filament\Resources\PromoHits\PromoHitResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPromoHit extends ViewRecord
{
    protected static string $resource = PromoHitResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
<?php

namespace App\Filament\Widgets;

use App\Models\ApplySubmission;
use App\Models\ContactSubmission;
use App\Models\PromoHit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteActivityOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'Site activiteit';

    protected ?string $description = 'Snelle totalen uit het CMS';

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Aantal promohits', number_format(PromoHit::query()->count(), 0, ',', '.'))
                ->description('Totaal geregistreerde promo redirects')
                ->color('warning'),
            Stat::make('Aantal ingevulde contactformulieren', number_format(ContactSubmission::query()->count(), 0, ',', '.'))
                ->description('Totaal ontvangen contactinzendingen')
                ->color('success'),
            Stat::make('Aantal ingevulde aanvraagformulieren', number_format(ApplySubmission::query()->count(), 0, ',', '.'))
                ->description('Totaal ontvangen aanvraaginzendingen')
                ->color('info'),
        ];
    }
}
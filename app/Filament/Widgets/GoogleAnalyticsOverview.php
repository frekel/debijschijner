<?php

namespace App\Filament\Widgets;

use App\Services\GoogleAnalytics\GoogleAnalyticsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GoogleAnalyticsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'Google Analytics';

    protected ?string $description = 'GA4 overzicht van de afgelopen 30 dagen';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $overview = app(GoogleAnalyticsService::class)->overview();

        if (! $overview['configured']) {
            return [
                Stat::make('GA4 nog niet gekoppeld', 'Configuratie vereist')
                    ->description($overview['message'])
                    ->color('warning'),
            ];
        }

        if ($overview['message']) {
            return [
                Stat::make('GA4 tijdelijk niet beschikbaar', 'Controleer koppeling')
                    ->description($overview['message'])
                    ->color('danger'),
            ];
        }

        $metrics = $overview['metrics'];

        return [
            Stat::make('Actieve gebruikers', $metrics['activeUsers'] ?? '0')
                ->description('Afgelopen 30 dagen')
                ->color('success'),
            Stat::make('Nieuwe gebruikers', $metrics['newUsers'] ?? '0')
                ->description('Afgelopen 30 dagen')
                ->color('info'),
            Stat::make('Sessies', $metrics['sessions'] ?? '0')
                ->description('Afgelopen 30 dagen')
                ->color('primary'),
            Stat::make('Paginaweergaven', $metrics['screenPageViews'] ?? '0')
                ->description('Afgelopen 30 dagen')
                ->color('gray'),
            Stat::make('Conversies', $metrics['conversions'] ?? '0')
                ->description('Afgelopen 30 dagen')
                ->color('warning'),
            Stat::make('Gem. sessieduur', $metrics['averageSessionDuration'] ?? '0:00')
                ->description('Minuten:seconden')
                ->color('success'),
        ];
    }
}
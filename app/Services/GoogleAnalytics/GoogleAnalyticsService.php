<?php

namespace App\Services\GoogleAnalytics;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleAnalyticsService
{
    public function isConfigured(): bool
    {
        return filled($this->propertyId())
            && filled($this->serviceAccountPath())
            && is_file($this->serviceAccountPath());
    }

    /**
     * @return array<string, mixed>
     */
    public function overview(int $days = 30): array
    {
        if (! $this->isConfigured()) {
            return [
                'configured' => false,
                'message' => 'Stel GOOGLE_ANALYTICS_PROPERTY_ID en GOOGLE_ANALYTICS_SERVICE_ACCOUNT_JSON in om GA4-cijfers te tonen.',
                'metrics' => [],
            ];
        }

        $cacheKey = sprintf('google-analytics.overview.%s.%d', $this->propertyId(), $days);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($days): array {
            try {
                $response = Http::withToken($this->accessToken())
                    ->acceptJson()
                    ->post(sprintf(
                        'https://analyticsdata.googleapis.com/v1beta/properties/%s:runReport',
                        $this->propertyId(),
                    ), [
                        'dateRanges' => [[
                            'startDate' => sprintf('%ddaysAgo', $days),
                            'endDate' => 'today',
                        ]],
                        'metrics' => [
                            ['name' => 'activeUsers'],
                            ['name' => 'newUsers'],
                            ['name' => 'sessions'],
                            ['name' => 'screenPageViews'],
                            ['name' => 'conversions'],
                            ['name' => 'averageSessionDuration'],
                        ],
                    ]);

                $response->throw();

                return [
                    'configured' => true,
                    'message' => null,
                    'metrics' => $this->mapMetrics($response->json()),
                ];
            } catch (\Throwable $exception) {
                report($exception);

                return [
                    'configured' => true,
                    'message' => 'GA4-data kon niet worden opgehaald. Controleer de property-ID, service account en API-toegang.',
                    'metrics' => [],
                ];
            }
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, string>
     */
    protected function mapMetrics(array $payload): array
    {
        $metricHeaders = Arr::pluck($payload['metricHeaders'] ?? [], 'name');
        $metricValues = Arr::pluck($payload['rows'][0]['metricValues'] ?? [], 'value');
        $metrics = [];

        foreach ($metricHeaders as $index => $name) {
            $value = $metricValues[$index] ?? '0';

            $metrics[$name] = match ($name) {
                'averageSessionDuration' => $this->formatDuration((float) $value),
                'conversions' => number_format((float) $value, 1, ',', '.'),
                default => number_format((int) round((float) $value), 0, ',', '.'),
            };
        }

        return $metrics;
    }

    protected function accessToken(): string
    {
        $credentials = $this->serviceAccountCredentials();
        $now = time();
        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']) ?: '{}');
        $claims = $this->base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ]) ?: '{}');
        $unsignedToken = $header.'.'.$claims;

        if (! openssl_sign($unsignedToken, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Kon Google service account JWT niet ondertekenen.');
        }

        $assertion = $unsignedToken.'.'.$this->base64UrlEncode($signature);

        $response = Http::asForm()
            ->acceptJson()
            ->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ]);

        $response->throw();

        return (string) data_get($response->json(), 'access_token');
    }

    /**
     * @return array{client_email: string, private_key: string}
     */
    protected function serviceAccountCredentials(): array
    {
        $path = $this->serviceAccountPath();

        if (! is_file($path)) {
            throw new RuntimeException('Google Analytics service account JSON ontbreekt.');
        }

        $credentials = json_decode((string) file_get_contents($path), true);

        if (! is_array($credentials)) {
            throw new RuntimeException('Google Analytics service account JSON is ongeldig.');
        }

        $clientEmail = data_get($credentials, 'client_email');
        $privateKey = data_get($credentials, 'private_key');

        if (! is_string($clientEmail) || ! is_string($privateKey)) {
            throw new RuntimeException('Google Analytics service account mist client_email of private_key.');
        }

        return [
            'client_email' => $clientEmail,
            'private_key' => $privateKey,
        ];
    }

    protected function propertyId(): ?string
    {
        $propertyId = config('services.google_analytics.property_id');

        return is_string($propertyId) && $propertyId !== '' ? $propertyId : null;
    }

    protected function serviceAccountPath(): ?string
    {
        $path = config('services.google_analytics.service_account_json');

        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        if (Str::startsWith($path, ['/'])) {
            return $path;
        }

        return base_path($path);
    }

    protected function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    protected function formatDuration(float $seconds): string
    {
        $roundedSeconds = max(0, (int) round($seconds));
        $minutes = intdiv($roundedSeconds, 60);
        $remainingSeconds = $roundedSeconds % 60;

        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }
}
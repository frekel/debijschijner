<?php

namespace App\Filament\Resources\ApplySubmissions\Pages;

use App\Filament\Resources\ApplySubmissions\ApplySubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListApplySubmissions extends ListRecords
{
    protected static string $resource = ApplySubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

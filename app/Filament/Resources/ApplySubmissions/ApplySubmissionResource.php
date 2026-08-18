<?php

namespace App\Filament\Resources\ApplySubmissions;

use App\Filament\Resources\ApplySubmissions\Pages\ListApplySubmissions;
use App\Filament\Resources\ApplySubmissions\Schemas\ApplySubmissionForm;
use App\Filament\Resources\ApplySubmissions\Tables\ApplySubmissionsTable;
use App\Models\ApplySubmission;
use Illuminate\Database\Eloquent\Model;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ApplySubmissionResource extends Resource
{
    protected static ?string $model = ApplySubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $slug = 'request-forms';

    protected static ?string $recordTitleAttribute = 'email';

    protected static ?string $navigationLabel = 'Aanvraag formulier';

    protected static ?string $modelLabel = 'Aanvraag formulier';

    protected static ?string $pluralModelLabel = 'Aanvraag formulieren';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ApplySubmissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplySubmissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplySubmissions::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}

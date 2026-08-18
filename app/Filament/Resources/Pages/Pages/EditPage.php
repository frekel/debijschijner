<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveHeader')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action('save')
                ->keyBindings(['mod+s']),
            ViewAction::make()
                ->url(fn (): string => '/'.trim((string) ($this->getRecord()->slug ?? ''), '/'))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}

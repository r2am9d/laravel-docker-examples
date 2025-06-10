<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

final class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->color('gray')
                ->icon('far-left')
                ->url(self::$resource::getUrl('index')),
            Actions\EditAction::make()
                ->icon('far-pencil'),
            Actions\DeleteAction::make()
                ->icon('far-trash'),
        ];
    }
}

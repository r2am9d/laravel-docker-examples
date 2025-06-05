<?php

declare(strict_types=1);

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

final class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Published' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', true)),
            'Unpublished' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

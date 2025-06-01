<?php

declare(strict_types=1);

namespace App\Traits;

use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

trait HasHotReload
{
    public function register(): void
    {
        parent::register();

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn () => Blade::render("@vite('resources/js/hot-reload.js')"),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Panel;

final class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'far-house';

    public static function getNavigationIcon(): string
    {
        return self::$navigationIcon;
    }

    public function panel(Panel $panel): Panel
    {
        return $panel;
    }
}

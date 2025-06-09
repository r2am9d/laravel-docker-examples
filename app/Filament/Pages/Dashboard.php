<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Panel;
use Filament\Support\Enums\Alignment;

final class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'far-house';

    public static function getNavigationIcon(): string
    {
        return self::$navigationIcon;
    }

    public function panel(Panel $panel): Panel
    {
        return $panel;
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Section::make('Filters')
                ->collapsible()
                ->icon('far-filters')
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->reactive(),
                    DatePicker::make('startDate')
                        ->label('Start Date')
                        ->reactive(),
                    DatePicker::make('endDate')
                        ->label('End Date')
                        ->reactive(),
                ],
                )
                ->footerActionsAlignment(Alignment::Right)
                ->footerActions([
                        Action::make('resetFilters')
                            ->label('Reset Filters')
                            ->color('success')
                            ->icon('far-rotate-right')
                            // ->iconPosition(IconPosition::After)
                            ->action(fn () => $this->resetFilters()),
                    ])
                ->columns(3),
        ]);
    }

    private function resetFilters(): void
    {
        $this->filters = [
            'name' => '',
            'startDate' => '',
            'endDate' => '',
        ];
    }
}

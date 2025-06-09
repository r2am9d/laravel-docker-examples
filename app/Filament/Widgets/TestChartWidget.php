<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

final class TestChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Users';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'];
        $endDate = $this->filters['endDate'];

        $data = Trend::model(User::class)
            ->between(
                start: $startDate ? Carbon::parse($startDate) : now()->startOfYear(),
                end: $endDate ? Carbon::parse($endDate) : now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'No of users/month',
                    'data' => $data->map(fn (TrendValue $value): mixed => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value): string => Carbon::createFromFormat('Y-m', $value->date)
                ->format('M')),
        ];
    }

    /**
     * Get chart options
     *
     * @return array<mixed>
     */
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'precision' => 0, // ✅ This removes decimals
                        'beginAtZero' => true, // optional, improves clarity
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

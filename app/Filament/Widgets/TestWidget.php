<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class TestWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today()->toDateString();

        $result = DB::table('users')
            ->selectRaw('
                COUNT(*) AS total,
                COUNT(*) FILTER (WHERE created_at::date = ?) AS today_count,
                COUNT(*) FILTER (WHERE created_at::date < ?) AS prev_count
            ', [$today, $today])
            ->first();

        [$totalUsers, $todayCount, $previousCount] = [
            $result->total,
            $result->today_count,
            $result->prev_count,
        ];

        // Step 3: Calculate percentage change (allowing negative results)
        if ($previousCount > 0) {
            $percentageChange = round((($todayCount - $previousCount) / $previousCount) * 100, 2);
        } elseif ($todayCount > 0) {
            $percentageChange = 100; // If no users before but some today
        } else {
            $percentageChange = 0; // No users at all
        }

        $trend = '';
        if ($percentageChange > 0) {
            $trend = 'positive';
        } elseif ($percentageChange < 0) {
            $trend = 'negative';
        } else {
            $trend = 'neutral';
        }

        // Map values based on trend
        $description = ($trend === 'positive' ? '+' : '').$percentageChange.'% vs all previous signups';

        $descriptionIcon = match ($trend) {
            'positive' => 'far-arrow-trend-up',
            'negative' => 'far-arrow-trend-down',
            'neutral' => 'far-minus',
            // default => '',
        };

        $color = match ($trend) {
            'positive' => 'success',
            'negative' => 'danger',
            'neutral' => 'gray',
            // default => '',
        };

        return [
            Stat::make('No of Users', $totalUsers)
                ->icon('far-users')
                ->description($description)
                ->descriptionIcon($descriptionIcon)
                ->color($color)
                ->chart([$previousCount, $todayCount]),
        ];
    }
}

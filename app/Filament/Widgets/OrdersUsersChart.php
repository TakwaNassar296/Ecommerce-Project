<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Carbon\Carbon;

class OrdersUsersChart extends ChartWidget
{
    protected ?string $heading = 'Orders Users Chart';

    protected static ?int $sort = 2;

    
    protected function getType(): string
    {
        return 'line';
    }

    protected ?string $maxHeight = '300px';

    
    protected function getData(): array
    {
        $months = collect(range(5,0))
            ->map(fn($i) => now()->subMonths($i));

        $labels = $months->map(fn($m) => $m->format('F'))->toArray();

        $usersData = $months->map(fn($m) => User::whereYear('created_at', $m->year)
                                                  ->whereMonth('created_at', $m->month)
                                                  ->count())->toArray();

        if (count(array_filter($usersData)) === 0) {
            $usersData = array_fill(0, count($months), 0);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'مستخدمين جدد',
                    'data' => $usersData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 5,
                ],
            ],
        ];
    }

    
    protected function getOptions(): array
    {
        return [
            'animation' => [
                'duration' => 0, 
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'min' => 0,
                    'max' => max(array_merge([0], $this->getData()['datasets'][0]['data'])) + 1,
                ],
            ],
        ];
    }

    protected ?string $pollingInterval = '10s';
    

}
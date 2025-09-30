<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use PhpParser\Node\Expr\AssignOp\ShiftLeft;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{

    protected function getColumns(): int|array|null
    {
        return 2;
    }
    
    protected function getStats(): array
    {
        return [
            Stat::make('Customers Count' , User::count())
            ->description('Count Of Users')
            ->descriptionIcon('heroicon-o-users',  IconPosition::Before )
            ->color('success')
            ->chart(
                collect(range(6,0))
                ->map(fn($i) => User::whereDate('created_at' , Carbon::today()->subDays($i))->count())
                ->toArray()
            )
            ->extraAttributes([
                'style' =>'height : 150px',
            ]),

            Stat::make('Orders Count' , Order::count())
            ->description('Count Of Orders')
            ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
            ->color('success')
            ->chart(
                collect(range(6,0))
                ->map(fn($i) => Order::whereDate('created_at' , Carbon::today()->subDays($i))->count())
                ->toArray()
            )
            ->extraAttributes([
                'style' =>'height : 150px',
            ])
        ];
    }
}

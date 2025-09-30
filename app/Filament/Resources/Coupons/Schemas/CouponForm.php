<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('code')
                        ->required()
                        ->unique(ignoreRecord:true)
                        ->maxlength(50),
                    Select::make('type')
                        ->options(['fixed' => 'Fixed', 'percentage' => 'Percentage'])
                        ->required(),
                    TextInput::make('value')
                        ->required()
                        ->numeric(),
                    DateTimePicker::make('expires_at'),
                    TextInput::make('usage_limit')
                        ->numeric()
                        ->default(null),
                    TextInput::make('used_count')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_active')
                        ->default(true),
                ])
                ->columnSpanFull()
                ->columns(2)
            ]);
    }
}

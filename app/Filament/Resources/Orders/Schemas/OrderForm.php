<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\User;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    select::make('user_id')
                        ->label('Client Name')
                        ->required()
                        ->options(User::pluck('name' , 'id')),
                    TextInput::make('total_price')
                        ->required()
                        ->numeric(),
                    Select::make('payment_status')
                        ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'])
                        ->default('pending')
                        ->required(),   
                ])
                ->columnSpanFull()
                ->columns(2)
            ]);
    }
}

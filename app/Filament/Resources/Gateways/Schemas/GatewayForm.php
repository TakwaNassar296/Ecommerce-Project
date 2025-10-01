<?php

namespace App\Filament\Resources\Gateways\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class GatewayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->required()
                        ->unique(ignoreRecord:true),
                    Toggle::make('is_active')
                        ->required()
                        ->inline(false),
                ])
                ->columnSpanFull()
                ->columns(2)
            ]);
    }
}

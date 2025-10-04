<?php

namespace App\Filament\Resources\Gateways\Schemas;

use Filament\Forms\Components\Textarea;
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
                        ->required(),
                    Toggle::make('is_active')
                        ->required()
                        ->inline(false),

                    Textarea::make('api_key')
                        ->label('Api Key')
                        ->required(),
                    Toggle::make('is_test')
                        ->label('Test Mode')
                        ->helperText('Enable this if you are using sandbox/test mode.')
                        ->inline(false),    
                ])
                ->columnSpanFull()
                ->columns(2)
            ]);
    }
}

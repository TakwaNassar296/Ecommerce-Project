<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('domain')
                        ->default(null),
                ])
                ->columnSpanFull()
                ->columns(2)        
            ]);
    }
}

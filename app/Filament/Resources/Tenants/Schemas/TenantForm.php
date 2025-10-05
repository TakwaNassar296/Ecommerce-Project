<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
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
                    FileUpload::make('logo')
                        ->image()
                        ->directory('tenants')
                        ->disk('public')
                        ->reorderable()
                        ->downloadable() ,
                    TextInput::make('email')
                        ->email(),
                    TextInput::make('phone'),
                    TextInput::make('address'),
                    TextInput::make('currency')
                        ->default('EGY'),
                    Textarea::make('description')
                        ->rows(3)             
                          
                ])
                ->columnSpanFull()
                ->columns(2)        
            ]);
    }
}

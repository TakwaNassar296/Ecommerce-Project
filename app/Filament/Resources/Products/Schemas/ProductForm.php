<?php

namespace App\Filament\Resources\Products\Schemas;

use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('name')
                        ->required()
                        ->reactive()
                        ->lazy()
                        ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),
                    Textarea::make('description')
                        ->default(null)
                        ->columnSpanFull(),
                    TextInput::make('slug')
                        ->required()
                        ->readOnly()
                        ->unique(ignoreRecord: true),
                    FileUpload::make('image')
                        ->image()
                        ->directory('products')
                        ->disk('public')
                        ->reorderable()
                        ->downloadable() ,
                    Select::make('category_id')
                        ->required()
                        ->relationship('category' , 'name'),
                ])->columnSpanFull()
                ->columns(2),        
            ]);
    }
}

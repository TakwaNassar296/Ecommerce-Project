<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class CategoryForm
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
                    TextInput::make('slug')
                        ->required()
                        ->readOnly()
                        ->unique(ignoreRecord: true),
                    Select::make('parent_id')
                        ->label('Parent Category')
                        ->options(Category::pluck('name', 'id'))
                        ->default(null),
                ])->columnSpanFull()
                ->columns(2),
            ]);
    }
}

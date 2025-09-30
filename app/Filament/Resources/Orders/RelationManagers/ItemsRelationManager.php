<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\ProductVariant;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\DissociateBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Select::make('product_variant_id')
                        ->required()
                        ->options(ProductVariant::pluck('sku' , 'id'))
                        ->label('Variant Sku'),
                    TextInput::make('quantity')
                        ->required()
                        ->numeric(),
                    TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('$'),
                    TextInput::make('total')
                        ->label('Total')
                        ->disabled()
                        ->dehydrated(false)
                        ->reactive()
                        ->afterStateHydrated(fn($state , callable $set ,$get) =>
                           $set('total' , $get('price') * $get('quantity'))
                        )
                        ->afterStateUpdated(fn($state , callable $set ,$get) =>
                           $set('total' , $get('price') * $get('quantity'))
                        ),    
                ]) 
                ->columnSpanFull()
                ->columns(2)   
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('OrderItem')
            ->columns([
                TextColumn::make('variant.sku')
                    ->label('Variant Sku'),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->getStateUsing(fn($record) => $record->price * $record->quantity )
                    ->summarize(Sum::make()),    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}

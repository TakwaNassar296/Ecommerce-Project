<?php

namespace App\Filament\Resources\Gateways;

use App\Filament\Resources\Gateways\Pages\CreateGateway;
use App\Filament\Resources\Gateways\Pages\EditGateway;
use App\Filament\Resources\Gateways\Pages\ListGateways;
use App\Filament\Resources\Gateways\Pages\ViewGateway;
use App\Filament\Resources\Gateways\Schemas\GatewayForm;
use App\Filament\Resources\Gateways\Schemas\GatewayInfolist;
use App\Filament\Resources\Gateways\Tables\GatewaysTable;
use App\Models\Gateway;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GatewayResource extends Resource
{
    protected static ?string $model = Gateway::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Gateway';

    public static function form(Schema $schema): Schema
    {
        return GatewayForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GatewayInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GatewaysTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGateways::route('/'),
            'create' => CreateGateway::route('/create'),
            'view' => ViewGateway::route('/{record}'),
            'edit' => EditGateway::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

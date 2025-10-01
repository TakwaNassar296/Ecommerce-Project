<?php

namespace App\Filament\Resources\Gateways\Pages;

use App\Filament\Resources\Gateways\GatewayResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGateway extends ViewRecord
{
    protected static string $resource = GatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

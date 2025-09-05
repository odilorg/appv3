<?php

namespace App\Filament\Resources\TransportRateResource\Pages;

use App\Filament\Resources\TransportRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransportRates extends ListRecords
{
    protected static string $resource = TransportRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

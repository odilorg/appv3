<?php

namespace App\Filament\Resources\RateItemResource\Pages;

use App\Filament\Resources\RateItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRateItem extends EditRecord
{
    protected static string $resource = RateItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

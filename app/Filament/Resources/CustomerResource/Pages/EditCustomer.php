<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Enums\CustomerType;
use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($record->type === CustomerType::INDIVIDUAL) {
            $record->companyProfile()?->delete();
            $record->individualProfile()->firstOrCreate([]);
        } else {
            $record->individualProfile()?->delete();
            $record->companyProfile()->firstOrCreate([]);
        }
    }
}

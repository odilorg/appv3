<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Enums\CustomerType;
use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function afterCreate(): void
    {
        // Belt & suspenders: ensure exactly one profile exists and the other does not.
        $record = $this->record;

        if ($record->type === CustomerType::INDIVIDUAL) {
            $record->companyProfile()?->delete();
            $record->individualProfile()->firstOrCreate([]);
        } else {
            $record->individualProfile()?->delete();
            $record->companyProfile()->firstOrCreate(
                // If the user didn’t pick a company in the subform, you can create a blank link here,
                // but we required company_id in the form, so this should already exist.
                []
            );
        }
    }
}

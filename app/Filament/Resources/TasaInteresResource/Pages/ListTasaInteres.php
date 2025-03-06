<?php

namespace App\Filament\Resources\TasaInteresResource\Pages;

use App\Filament\Resources\TasaInteresResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTasaInteres extends ListRecords
{
    protected static string $resource = TasaInteresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

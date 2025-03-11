<?php

namespace App\Filament\Resources\MovimientosResource\Pages;

use App\Filament\Resources\MovimientosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMovimientos extends ListRecords
{
    protected static string $resource = MovimientosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

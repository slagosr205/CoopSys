<?php

namespace App\Filament\Resources\MovimientosResource\Pages;

use App\Filament\Resources\MovimientosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMovimientos extends EditRecord
{
    protected static string $resource = MovimientosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

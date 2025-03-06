<?php

namespace App\Filament\Resources\TasaInteresResource\Pages;

use App\Filament\Resources\TasaInteresResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTasaInteres extends EditRecord
{
    protected static string $resource = TasaInteresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

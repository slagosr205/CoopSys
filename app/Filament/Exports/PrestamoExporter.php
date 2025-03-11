<?php

namespace App\Filament\Exports;

use App\Models\Prestamo;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrestamoExporter extends Exporter
{
    protected static ?string $model = Prestamo::class;

    public static function getColumns(): array
    {
        return [
            //
            ExportColumn::make('prestamo_id'),
            ExportColumn::make('cliente.nombre'),
            ExportColumn::make('monto_solicitado'),
            ExportColumn::make('estado')
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prestamo export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}

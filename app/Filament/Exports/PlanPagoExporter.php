<?php

namespace App\Filament\Exports;

use App\Models\PlanPago;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PlanPagoExporter extends Exporter
{
    protected static ?string $model = PlanPago::class;

    public static function getColumns(): array
    {
        return [
            //
            ExportColumn::make('fecha_pago')->label('Fecha de Pago'),
            ExportColumn::make('cuota')->label('Cuota'),
            ExportColumn::make('interes')->label('Interes'),
            ExportColumn::make('capital')->label('Capital'),
            ExportColumn::make('saldo')->label('Saldo'),
            ExportColumn::make('estado')->label('Estado'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your plan pago export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}

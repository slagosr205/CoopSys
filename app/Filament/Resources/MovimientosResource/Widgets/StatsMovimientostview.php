<?php

namespace App\Filament\Resources\MovimientosResource\Widgets;

use App\Casts\MoneyCast;
use App\Livewire\ModalMovimientos;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\Transaccion;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\TableComponent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsMovimientostview extends BaseWidget
{
    protected function getStats(): array
    {
        $ingresosPorDepositos = Transaccion::where('tipo', 'Deposito')->sum('monto');
        $ingresosPorPagos = Pago::sum('monto_pago');
        $totalIngresos = $ingresosPorDepositos + $ingresosPorPagos;

        // SALIDAS
        $salidasPorRetiros = Transaccion::where('tipo', 'Retiro')->sum('monto');
        $salidasPorPrestamos = Prestamo::sum('monto_aprobado');
        $totalSalidas = $salidasPorRetiros + $salidasPorPrestamos;
          // BALANCE
          $balance = $totalIngresos - $totalSalidas;
        return [
            //
           Stat::make('Ingresos Totales','Lps. '.number_format($totalIngresos,2))
           ->description('Depósitos y pagos de préstamos')
           ->color('success')
           ->extraAttributes([
            'class' => 'cursor-pointer',
            
           ]),

           
           //(fn () => $this->openModal('Ingresos')),

        ];
    }

       // Función para abrir el modal con datos dinámicos
       protected function openModal($tipo)
       {
           $this->emit('openModal', $tipo);
       }

       // Definir la tabla dentro del modal
    public function table(TableComponent $table): TableComponent
    {
        return $table
            ->query(Transaccion::query()) // Se inicializa con todas las transacciones
            ->columns([
                TextColumn::make('tipo')->label('Tipo de Movimiento')->sortable(),
                TextColumn::make('monto')->label('Monto')->sortable(),
                TextColumn::make('fecha_transaccion')->label('Fecha')->sortable(),
            ])
            ->actions([
                Action::make('verDetalles')
                    ->label('Ver más')
                    ->icon('heroicon-o-eye')
                    ->modal()
                    ->form([
                        TextColumn::make('tipo')->label('Tipo de Movimiento'),
                        TextColumn::make('monto')->label('Monto'),
                        TextColumn::make('fecha_transaccion')->label('Fecha'),
                    ])
            ]);
    }
}

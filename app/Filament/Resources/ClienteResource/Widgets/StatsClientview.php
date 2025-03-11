<?php

namespace App\Filament\Resources\ClienteResource\Widgets;

use App\Models\Cliente;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsClientview extends BaseWidget
{
    protected ?string $heading = 'Analytics';
    
    protected function getStats(): array
    {
        /* It's lineas calculating the total number of clients registered in the
        system, the total number of clients registered this month, and the total number of clients
        registered last month. */
        $totalClientes=Cliente::count();
        $totalClientesEsteMes = Cliente::whereMonth('created_at', now()->month)->count();
        $totalClientesMesPasado = Cliente::whereMonth('created_at', now()->subMonth()->month)->count();

        // Calcular incremento porcentual
        if ($totalClientesMesPasado > 0) {
            $incrementoPorcentual = (($totalClientesEsteMes - $totalClientesMesPasado) / $totalClientesMesPasado) * 100;
        } else {
            $incrementoPorcentual = 0; // Si no hay clientes el mes pasado, no hay incremento
        }

        // Formatear incremento a dos decimales
        $incrementoPorcentual = number_format($incrementoPorcentual, 2);

        // Obtener el número de clientes registrados por mes en los últimos 2 meses
        $clientesPorMes = Cliente::selectRaw('COUNT(*) as total, MONTH(created_at) as mes, YEAR(created_at) as anio')
        ->whereBetween('created_at', [
            Carbon::now()->subMonths(1)->startOfMonth(), // Comienza desde el inicio del mes pasado
            Carbon::now()->endOfMonth() // Hasta el final del mes actual
        ])
        ->groupBy('anio', 'mes')
        ->orderBy('anio', 'desc')
        ->orderBy('mes', 'desc')
        ->take(2) // Tomamos solo los dos últimos meses
        ->get();

        // Asegurándonos de que tenemos al menos dos meses de datos
        if ($clientesPorMes->count() == 2) {
            // Obtener los datos de los dos últimos meses
            $mesAnterior = $clientesPorMes[1]->total;
            $mesActual = $clientesPorMes[0]->total;

            // Calcular el cambio porcentual
            $cambio = (($mesActual - $mesAnterior) / $mesAnterior) * 100;

            // Determinar si está aumentando o disminuyendo
            if ($cambio > 0) {
                $tendencia = "Aumento del " . round($cambio, 2) . "% en los registros de clientes";
                $icono = 'heroicon-m-arrow-trending-up';
            } elseif ($cambio < 0) {
                $tendencia = "Disminución del " . round(abs($cambio), 2) . "% en los registros de clientes";
                $icono = 'heroicon-m-arrow-trending-down';
            } else {
                $tendencia = "Sin cambio en los registros de clientes";
                $icono = 'heroicon-m-arrow-right';
            }

            return [
                //
                Stat::make('Clientes Registrados',$totalClientes ) 
                ->description("Incremento: " . $incrementoPorcentual . '%')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([$mesAnterior, $mesActual]),  // Se puede usar para mostrar los dos últimos meses en la gráfica
                Stat::make('Aumento de Clientes', '21%'),
                Stat::make('Promedio de Cliente', '3:12'),
            ];
        }else{
            return [
                //
                Stat::make('Clientes Registrados',$totalClientes ) 
                ->description("Incremento: " . $incrementoPorcentual . '%')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([0, 0]),  // Se puede usar para mostrar los dos últimos meses en la gráfica
                Stat::make('Aumento de Clientes', $incrementoPorcentual.'%'),
                Stat::make('Promedio de Cliente', '3:12'),
            ];
        }

        
    }
}

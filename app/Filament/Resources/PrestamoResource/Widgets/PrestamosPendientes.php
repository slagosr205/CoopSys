<?php

namespace App\Filament\Resources\PrestamoResource\Widgets;

use App\Filament\Resources\PrestamoResource;
use App\Filament\Resources\PrestamoResource\Pages\CreatePrestamo;
use App\Models\Prestamo;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class PrestamosPendientes extends BaseWidget
{
    protected int | string | array $columnSpan='full';
    protected static ?int $sort=4;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                // ...
              //  Prestamo::query()->where('estado','pendiente'),
              PrestamoResource::getEloquentQuery()->where('estado','pendiente'),
            )->defaultPaginationPageOption(5)
            ->defaultSort('created_at','desc')
            ->columns([
                // ...
                TextColumn::make('prestamo_id')->label('prestamo ID'),
                TextColumn::make('cliente.nombre')->label('Cliente')->searchable(), // Mostramos el nombre del cliente
                TextColumn::make('monto_solicitado')->label('Monto Solicitado')->money('HNL')
                ->badge()
                ->color(fn ($record) => match ($record->estado) {
                    'pendiente' => 'warning',  // Amarillo
                    'aprobado'  => 'success',  // Verde
                    'rechazado' => 'danger',   // Rojo
                    default => 'gray',         // Por si hay otros estados
                }),
            ])->emptyStateDescription('No hay préstamos por aprobar')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('aprobar')
                ->label('Aprobar/Rechazar')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($record) => $record->estado === 'pendiente') // Solo visible si está en "Pendiente"
                ->form([
                    Wizard::make([
                        Step::make('Validar Información')
                            ->schema([
                                TextInput::make('cliente_nombre')
                                ->label('Cliente')
                                ->default(fn ($record) => $record->cliente->nombre)
                                ->disabled(),
                            TextInput::make('monto_solicitado')
                                ->label('Monto Solicitado')
                                ->default(fn ($record) => $record->monto_solicitado)
                                ->disabled(),
                            TextInput::make('fecha_solicitud')
                                ->label('Fecha de Solicitud')
                                ->default(fn ($record) => $record->fecha_solicitud)
                                ->disabled(),
                                TextInput::make('plazo_meses')
                                ->label('Plazo')
                                ->default(fn ($record) => $record->plazo_meses)
                                ->disabled(),
                            ]),
                            Step::make('Definir Aprobación')->schema([
                                Select::make('estado')
                                    ->label('Estado de Aprobación')
                                    ->options([
                                        'aprobado' => 'Aprobado',
                                        'rechazado' => 'Rechazado'
                                    ])
                                    ->required()
                                    ->reactive(),
                                TextInput::make('monto_aprobado')
                                    ->label('Monto Aprobado')
                                    ->numeric()
                                    ->step(0.01)
                                    ->hidden(fn ($get) => $get('estado') !== 'aprobado'),
                                Textarea::make('comentarios')
                                    ->label('Comentarios')
                                    ->nullable(),
                            ]),
                            
                        ]),
                    ]),
                ]) ->emptyStateActions([
                    Tables\Actions\CreateAction::make()
                        ->model(Prestamo::class)
                        ->form([
                            TextInput::make('monto')->required(),
                            Select::make('estado')
                                ->options([
                                    'pendiente' => 'Pendiente',
                                    'aprobado' => 'Aprobado',
                                    'rechazado' => 'Rechazado',
                                ])
                                ->required(),
                        ])->label('crear nuevo prestamo')->icon('heroicon-o-plus-circle'),
                ]);
    }
}

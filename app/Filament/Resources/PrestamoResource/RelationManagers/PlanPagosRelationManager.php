<?php

namespace App\Filament\Resources\PrestamoResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class PlanPagosRelationManager extends RelationManager
{
    protected static string $relationship = 'planPagos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha_pago')
                ->label('Fecha de Pago')
                ->required(),

                Forms\Components\TextInput::make('cuota')
                ->label('Cuota')
                ->numeric()
                ->required(),

                Forms\Components\TextInput::make('interes')
                ->label('Interés')
                ->numeric()
                ->required(),

                Forms\Components\TextInput::make('capital')
                ->label('Capital')
                ->numeric()
                ->required(),

                Forms\Components\TextInput::make('saldo')
                ->label('Saldo')
                ->numeric()
                ->required(),

                Forms\Components\Select::make('estado')
                ->label('Estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'pagado' => 'Pagado',
                ])
                ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('prestamo_id')
            ->columns([
                Tables\Columns\TextColumn::make('fecha_pago')
                    ->label('Fecha de Pago')
                    ->sortable(),

                     // Columna calculada: "Estado de la cuota"
                    Tables\Columns\TextColumn::make('estado_cuota')
                    ->label('Estado de la Cuota')
                    ->getStateUsing(function ($record) {
                        $fecha_pago = Carbon::parse($record->fecha_pago);
                        $estado = $record->estado;
                         
                        // Compara si la fecha de pago está atrasada
                        if ($estado === 'pendiente') {
                            if ($fecha_pago->isPast()) {
                                return 'Atrasada';
                            } else {
                                return 'Vigente';
                            }
                        }
                        return 'Pagado'; // Si ya está pagado
                    })->color(function ($record) {
                        $fecha_pago = Carbon::parse($record->fecha_pago);
                        $estado = $record->estado;

                        // Compara si la fecha de pago está atrasada
                        if ($estado === 'pendiente') {
                            if ($fecha_pago->isPast()) {
                                return 'danger';
                            } else {
                                return 'warning';
                            }
                        }
                        return 'success'; // Si ya está pagado
                    })->badge(),
                     // Si deseas que sea ordenable

                    Tables\Columns\TextColumn::make('cuota')
                    ->label('Cuota')
                    ->money('HNL')
                    ->summarize(Sum::make()->label('Total a Pagar')->money('HNL'))
                    ->sortable(),

                    Tables\Columns\TextColumn::make('interes')
                    ->label('Interés')
                    ->money('HNL')
                    ->summarize(Sum::make()->label('Total Interes')->money('HNL'))
                    ->sortable(),
                    
                    Tables\Columns\TextColumn::make('capital')
                    ->label('Capital')
                    ->money('HNL')
                    ->summarize(Sum::make()->label('Total Capital')->money('HNL'))
                    ->sortable(),

                    Tables\Columns\TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money('HNL')
                    ->sortable(),

                    Tables\Columns\SelectColumn::make('estado')
                    ->label('Estado del Pago')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                    ]),
            ])
            ->filters([
                //
                 Tables\Filters\SelectFilter::make('estado')
                ->label('Estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'pagado' => 'Pagado',
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('efectuar Pago')->button()->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ]);
            ;
            
    }
}

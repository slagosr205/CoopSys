<?php

namespace App\Filament\Resources\TransaccionesResource\RelationManagers;

use App\Models\TasaInteres;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CuentasRelationManager extends RelationManager
{
    protected static string $relationship = 'cuentas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Usamos BelongsTo para la relación con clientes
                Forms\Components\Select::make('cliente_id')
                    ->relationship('cliente', 'nombre') // 'cliente' es la relación definida en el modelo
                    ->preload()
                    ->required() // Requerido
                    ->searchable() // Permite la búsqueda de clientes
                    ->label('Cliente'), // Etiqueta que se muestra en el formulari
                    TextInput::make('saldo')
                    ->label('Saldo')
                    ->required()
                    ->numeric() // Asegura que el valor sea un número
                    ->minValue(0)
                    ->step(0.01)
                    ->disabled(fn($livewire)=>$livewire instanceof EditRecord)
                    ->stripCharacters(',')
                    ->mask(RawJs::make('$money($input)')),

                    // En tu formulario, crea el campo select para seleccionar una tasa de interés
                    Select::make('tasa_interes_id')
                    ->relationship('tasasInteres','tasa_id')
                    ->preload()
                    ->label('Tasa de Interes')
                    ->required()
                    ->placeholder('Seleccione una tasa de interés')
                    ->searchable()
                    ->disabled(fn($livewire) => $livewire instanceof EditRecord),

                    // En tu formulario, crea el campo select para seleccionar una tasa de interés
                    Select::make('estado_cuenta')
                    ->label('Estado de la Cuenta')
                    ->options(
                        [
                            'activo'=>'Activo',
                            'inhabilitado'=>'Inhabilitado',
                        ]
                    )
                    ->hidden(fn($livewire) => $livewire instanceof CreateRecord)
                    ->searchable(),
                    
                    
                    DateTimePicker::make('fecha_apertura')
                    ->label('Fecha de Apertura')
                    ->required()
                    ,
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('cuenta_id')
            ->columns([
                Tables\Columns\TextColumn::make('cuenta_id'),
                Tables\Columns\TextColumn::make('saldo'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaResource\Pages;
use App\Filament\Resources\CuentaResource\RelationManagers;
use App\Filament\Resources\TransaccionesResource\RelationManagers\CuentasRelationManager;
use App\Filament\Resources\TransaccionResource\RelationManagers\ClientesRelationManager;
use App\Filament\Resources\TransaccionResource\RelationManagers\TransaccionesRelationManager;
use App\Models\Cuenta;
use App\Models\TasaInteres;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CuentaResource extends Resource
{
    protected static ?string $model = Cuenta::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('cliente_id')
                ->relationship('cliente','nombre')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('nombre'),
                    Forms\Components\TextInput::make('identificacion'),
                    Forms\Components\Select::make('tipo_identificacion')->options(['DNI','PASAPORTE','OTROS']),
                    Forms\Components\DatePicker::make('fecha_nacimiento'),
                    Forms\Components\Select::make('genero')->options(['M','F']),
                    Forms\Components\TextInput::make('direccion'),
                    Forms\Components\TextInput::make('telefono'),
                    
                    Forms\Components\Radio::make('es_socio')->label('Es socio?')->boolean(),
                ]),
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
                Select::make('tasa_id')
                ->label('Tasa de interés')
                ->options(
                    TasaInteres::all()->pluck('porcentaje', 'tasa_id')->toArray()
                )
                ->required()
                ->placeholder('Seleccione una tasa de interés')
                ->searchable()
                ->disabled(fn($livewire) => $livewire instanceof EditRecord),
                
                DateTimePicker::make('fecha_apertura')
                ->label('Fecha de Apertura')
                ->required()
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('cuenta_id')->label('Cuenta ID'),
                TextColumn::make('cliente.nombre')->label('Cliente'), // Mostramos el nombre del cliente
                TextColumn::make('saldo')->label('Saldo'),
                TextColumn::make('fecha_apertura')->label('Fecha de Apertura'),
                TextColumn::make('created_at')->label('Creado'),
                TextColumn::make('updated_at')->label('Actualizado'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
           TransaccionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCuentas::route('/'),
            'create' => Pages\CreateCuenta::route('/create'),
            'edit' => Pages\EditCuenta::route('/{record}/edit'),
        ];
    }
}

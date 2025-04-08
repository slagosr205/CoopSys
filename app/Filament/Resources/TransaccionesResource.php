<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaccionesResource\Pages;
use App\Filament\Resources\TransaccionesResource\RelationManagers;
use App\Models\Transaccion;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaccionesResource extends Resource
{
    protected static ?string $model = Transaccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('usuario_id_registro')
                    ->relationship('usuario', 'name') // Relación con usuario
                    ->default(auth()->id()) // Asignar automáticamente el usuario autenticado
                    ->disabled()
                    ->dehydrated(),

                Select::make('cuenta_id')
                ->relationship('cuenta','cuenta_id')
                ,
                
                Select::make('tipo')
                    ->options([
                        'Deposito' => 'Depósito',
                        'Retiro' => 'Retiro',
                    ])
                    ->required(),
                TextInput::make('monto')->numeric()->required(),
                DateTimePicker::make('fecha_transaccion')->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Reporte de Transacciones')
            ->columns([
                //
                Tables\Columns\TextColumn::make('transaccion_id'),
                Tables\Columns\TextColumn::make('cuenta.cliente.nombre')->searchable(),
                Tables\Columns\TextColumn::make('tipo')                              
                ->color(fn (string $state): string  =>match($state) {'Retiro' => 'danger' ,'Deposito'=> 'success','Interes'=>'warning'})
                ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('monto')
                ->color(fn ($record) => match($record->tipo){'Retiro' => 'danger' ,'Deposito'=> 'success','Interes'=>'warning'})
                ->weight(FontWeight::Bold)
                ->money('HNL'),
                Tables\Columns\TextColumn::make('fecha_transaccion')->date(),
            ])
            ->groups([
                'cuenta.cliente.nombre',
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransacciones::route('/'),
            'create' => Pages\CreateTransacciones::route('/create'),
            'edit' => Pages\EditTransacciones::route('/{record}/edit'),
        ];
    }
}

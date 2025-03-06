<?php

namespace App\Filament\Resources\TransaccionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaccionesRelationManager extends RelationManager
{
    protected static string $relationship = 'transacciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('usuario_id_registro')
                    ->relationship('usuario', 'name') // Relación con usuario
                    ->default(auth()->id()) // Asignar automáticamente el usuario autenticado
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'Deposito' => 'Depósito',
                        'Retiro' => 'Retiro',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('monto')->numeric()->required(),
                Forms\Components\DateTimePicker::make('fecha_transaccion')->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('transaccion_id')
            ->columns([
                Tables\Columns\TextColumn::make('transaccion_id'),
                Tables\Columns\TextColumn::make('tipo')
                ->color(fn (string $state): string  =>match($state) {'Retiro' => 'danger' ,'Deposito'=> 'success','Interes'=>'success'})
                ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('monto')
                ->color(fn ($record) => match($record->tipo){'Retiro' => 'danger' ,'Deposito'=> 'success','Interes'=>'success'})
                ->weight(FontWeight::Bold)
                ->money('HNL'),
                Tables\Columns\TextColumn::make('fecha_transaccion'),
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
            ])
            ;
    }
}

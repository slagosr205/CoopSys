<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Filament\Resources\TransaccionesResource\RelationManagers\CuentasRelationManager;
use App\Filament\Resources\TransaccionResource\RelationManagers\ClientesRelationManager;
use App\Models\Cliente;
use App\Models\Transacciones;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('nombre'),
                Forms\Components\TextInput::make('identificacion'),
                Forms\Components\Select::make('tipo_identificacion')->options(['DNI','PASAPORTE','OTROS']),
                Forms\Components\DatePicker::make('fecha_nacimiento'),
                Forms\Components\Select::make('genero')->options(['M','F']),
                Forms\Components\TextInput::make('direccion'),
                Forms\Components\TextInput::make('telefono'),
                Forms\Components\Radio::make('es_socio')->label('Es socio?')->boolean(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('identificacion'),
                TextColumn::make('nombre'),
                
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
            CuentasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}

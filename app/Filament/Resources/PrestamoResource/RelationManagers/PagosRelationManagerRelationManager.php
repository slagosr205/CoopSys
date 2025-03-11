<?php

namespace App\Filament\Resources\PrestamoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagosRelationManagerRelationManager extends RelationManager
{
    protected static string $relationship = 'pagos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                    TextInput::make('prestamo_id')
                    ->required()
                    ->maxLength(255),

                    TextInput::make('monto_pago')
                    ->label('Monto Pagado')
                    ->required()
                    ->numeric()
                    ->step(0.01),

                    DatePicker::make('fecha_pago')
                    ->label('Fecha de Pago')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('prestamo_id')
            ->columns([
                Tables\Columns\TextColumn::make('prestamo_id'),
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

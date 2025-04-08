<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TasaInteresResource\Pages;
use App\Filament\Resources\TasaInteresResource\RelationManagers;
use App\Models\TasaInteres;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasaInteresResource extends Resource
{
    protected static ?string $model = TasaInteres::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('porcentaje')
                    ->label('Porcentaje')
                    ->numeric()
                    ->required(),

                    Forms\Components\Select::make('tipo_tasa')
                    ->label('Tipo de Tasa')
                    ->options([
                        'fija' => 'Fija',
                        'variable' => 'Variable',
                    ])
                    ->required(),

                    Forms\Components\DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required(),

                    Forms\Components\DatePicker::make('fecha_fin')
                    ->label('Fecha de Fin')
                    ->nullable(),

                    Forms\Components\Select::make('destino')
                    ->label('Destino')
                    ->options([
                        'ahorro' => 'Ahorro',
                        'prestamo' => 'Préstamo',
                    ])
                    ->required(),

                    Forms\Components\Select::make('tipo_prestamo')
                    ->label('Tipo de Préstamo')
                    ->options([
                        'automatico' => 'Automático',
                        'personal' => 'Personal',
                        'garantia' => 'Garantía',
                    ])
                    ->nullable()
                   

                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([


                Tables\Columns\TextColumn::make('porcentaje')
                    ->label('Porcentaje')
                    ->sortable(),

                    
                    Tables\Columns\TextColumn::make('destino')
                    ->label('Destino')
                    ->badge()
                    ->sortable(),

                    Tables\Columns\TextColumn::make('tipo_tasa')
                    ->label('Tipo de Tasa')
                    ->sortable(),

                    Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->date(),

                    Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Fecha de Fin')
                    ->date()
                    ->sortable(),

                    

                    Tables\Columns\TextColumn::make('tipo_prestamo')
                    ->label('Tipo de Préstamo')
                    ->sortable(),
                    

                

            ])
            ->filters([
                //
                Tables\Filters\SelectFilter::make('destino')
                ->label('Destino')
                ->options([
                    'ahorro' => 'Ahorro',
                    'prestamo' => 'Préstamo',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->emptyStateHeading('No hay informacion ');
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
            'index' => Pages\ListTasaInteres::route('/'),
            'create' => Pages\CreateTasaInteres::route('/create'),
            'edit' => Pages\EditTasaInteres::route('/{record}/edit'),
        ];
    }
}

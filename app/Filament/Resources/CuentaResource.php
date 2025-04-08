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
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
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
                    Forms\Components\FileUpload::make('archivos')
                ->multiple()
                ->disk('public')
                ->directory(fn ($record) => $record ? 'documentos/' . $record->id : 'documentos/temp') // Usa 'temp' si aún no se ha creado el registro
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                ->storeFileNamesIn('archivos') // Guarda los nombres en la BD
                ->preserveFilenames() // Mantiene los nombres originales

                ->helperText('Debe subir el DNI y opcionalmente el RTN.'),

                Repeater::make('referencias_personales')
                ->label('Referencias Personales')
                ->relationship('referencias_personales') // Relación con el modelo Cliente
                ->schema([
                    TextInput::make('nombre_completo')
                        ->label('Nombre Completo')
                        ->required(),

                    Select::make('relacion')
                        ->label('Relación con el dueño')
                        ->options([
                            'hermano' => 'Hermano',
                            'esposo' => 'Esposo',
                            'esposa' => 'Esposa',
                            'amigo' => 'Amigo',
                            'otro' => 'Otro',
                            'madre'=>'Madre',
                            'padre'=>'Padre',
                            'hijo'=>'Hijo',
                        ])
                        ->required(),

                    DatePicker::make('fecha_nacimiento')
                        ->label('Fecha de Nacimiento')
                        ->required(),

                    TextInput::make('porcentaje_beneficio')
                        ->label('Porcentaje de Beneficio')
                        ->numeric()
                        ->suffix('%')
                        ->required(),
                    ])
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['nombre_completo'] ?? null) // Muestra el nombre en el encabezado
                ->addActionLabel('Agregar Referencia')
                ->columns(2),
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
                Select::make('tasa_interes_id')
                ->relationship('tasasInteres','destino')
                
                //->label('Tasa de interés')
                /*->options(
                    TasaInteres::all()->pluck('porcentaje', 'tasa_id')->toArray()
                )*/
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
                TextColumn::make('cliente.nombre')->label('Cliente')->searchable(), // Mostramos el nombre del cliente
                TextColumn::make('saldo')->label('Saldo')->money('HNL'),
                TextColumn::make('fecha_apertura')->label('Fecha de Apertura')->date(),
                TextColumn::make('created_at')->label('Creado')->date(),
                TextColumn::make('updated_at')->label('Actualizado')->date()->searchable(),
            ])
            ->filters([
                //
                Filter::make('fecha_apertura')
                ->form([
                    DatePicker::make('fecha_apertura')
                ])
                ->query(function(Builder $query, array $data): Builder{
                    return $query
                    ->when(

                        $data['fecha_apertura'],
                        fn (Builder $query, $date): Builder => $query->whereDate('fecha_apertura', '>=', $date),
                    )
                    ->when(
                        $data['fecha_apertura'],
                        fn (Builder $query, $date): Builder => $query->whereDate('fecha_apertura', '<=', $date),
                    );
                })
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

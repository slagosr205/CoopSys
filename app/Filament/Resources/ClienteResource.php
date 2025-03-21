<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Filament\Resources\TransaccionesResource\RelationManagers\CuentasRelationManager;
use App\Filament\Resources\TransaccionResource\RelationManagers\ClientesRelationManager;
use App\Filament\Widgets\GoogleMapField;
use App\Models\Cliente;
use App\Models\Transacciones;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Forms\Components\FileUpload::make('archivos')
                ->multiple()
                ->disk('public')
                ->directory(fn ($record) => $record ? 'documentos/' . $record->id : 'documentos/temp') // Usa 'temp' si aún no se ha creado el registro
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                ->rule(function () {
                    return function ($attribute, $value, $fail) {
                        $dniSubido = false;
            
                        foreach ($value as $archivo) {
                            if (str_contains($archivo->getClientOriginalName(), 'DNI')) {
                                $dniSubido = true;
                            }
                        }
            
                        if (!$dniSubido) {
                            $fail('Debe subir el documento de identidad (DNI).');
                        }
                    };
                })
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

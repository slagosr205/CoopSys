<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovimientosResource\Pages;
use App\Filament\Resources\MovimientosResource\RelationManagers;
use App\Models\Movimiento;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\Transaccion;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\select;

class MovimientosResource extends Resource
{
    protected static ?string $model = Movimiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('cajero_id_registro')
               ->relationship('usuario','name')
                ->default(auth()->id()) // Asignar automáticamente el usuario autenticado
                /*->disabled()*/
                ,
                Select::make('tipo_transaccion')
                ->label('Tipo de Transacción')
                ->options([
                    'pago_prestamo' => 'Pago Prestamo',
                    'deposito_ahorro' => 'Deposito',
                    'retiro_ahorro' => 'Retiro Ahorro',
                ])
                ->reactive(), // <-- Hace que se recarguen los valores dependientes

                Select::make('movimiento_prestamo_id')
                    ->label('Seleccionar Préstamo')
                    ->relationship(
                        'prestamo',
                        'prestamo_id',
                        fn ($query) => $query->with('cliente') // Cargar la relación del cliente
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "{$record->cliente->nombre} - Monto: HNL. {$record->monto_aprobado}"
                    )
                   
                    ->preload()
                    ->searchable()
                    ->reactive()
                    ->hidden(fn ($get) => $get('tipo_transaccion') !== 'pago_prestamo') // Se oculta si no es "pago_prestamo"
                    ->required(fn ($get) => $get('tipo_transaccion') === 'pago_prestamo') // Se vuelve obligatorio solo en pagos
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Verificar el estado y mostrar el ID del préstamo
                        $prestamoId = $get('movimiento_prestamo_id');
                        
                        // Verificar si tenemos el préstamo y su plan de pagos
                        if ($prestamoId) {
                            $prestamo = \App\Models\Prestamo::find($prestamoId);  // Obtiene el préstamo completo
                            $cuotaVigente = $prestamo->planPagos
                            ->where('estado', 'pendiente')
                            ->filter(function ($pago) {
                                $fechaPago = \Carbon\Carbon::parse($pago->fecha_pago); // Convertir a Carbon
                                return $fechaPago->between(now()->startOfMonth(), now()->endOfMonth());
                            })
                            ->first();
                            
                            $set('monto_ingresar', $cuotaVigente ? $cuotaVigente->cuota : 'No disponible');
                        }
                    }),

                    TextInput::make('monto_ingresar')
                    ->label('Monto')
                    ->hidden(fn ($get) => $get('tipo_transaccion') !== 'pago_prestamo')
                    ->reactive()
                    /*->disabled()*/,
                   // ->dehydrated(false),

                    
                    
                    
                     // No lo envía en el formulario,

            ]);


    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('movimiento_id'),
                TextColumn::make('usuario.name'),
                TextColumn::make('prestamo.cliente.nombre'),
                TextColumn::make('monto_ingresar')->money('HNL'),
                TextColumn::make('tipo_transaccion')->icon(fn($record)=>match($record->tipo_transaccion)
                {
                    'pago_prestamo'=>'heroicon-o-currency-dollar',
                }),
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
            'index' => Pages\ListMovimientos::route('/'),
            'create' => Pages\CreateMovimientos::route('/create'),
            'edit' => Pages\EditMovimientos::route('/{record}/edit'),
        ];
    }
}

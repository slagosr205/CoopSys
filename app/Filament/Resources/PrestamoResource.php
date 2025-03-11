<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PrestamoExporter;
use App\Filament\Resources\PrestamoResource\Pages;
use App\Filament\Resources\PrestamoResource\RelationManagers;
use App\Filament\Resources\PrestamoResource\RelationManagers\PagosRelationManagerRelationManager;
use App\Filament\Resources\PrestamoResource\RelationManagers\PlanPagosRelationManager;
use App\Models\Prestamo;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class PrestamoResource extends Resource
{
    protected static ?string $model = Prestamo::class;

   // protected static ?string $title='Solicitud de Prestamos';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('cliente_id')
                ->label('Cliente')
                ->relationship('cliente', 'nombre') // Relación con el cliente para mostrar su nombre
                ->required(),
                TextInput::make('monto_solicitado')
                ->label('Monto Solicitado')
                ->required()
                ->numeric() // Asegura que el monto sea un número
                
                ->step(0.01),
            DatePicker::make('fecha_solicitud')
                ->label('Fecha de Solicitud')
                ->required(),
            Select::make('estado')
                ->label('Estado')
                ->options(['pendiente'=>'Pendiente','aprobado'=>'Aprobado','rechazado'=>'Rechazado']),

            TextInput::make('plazo_meses')
                ->label('Plazo')
                ->required()
                ->numeric(), // Asegura que el monto sea un número

            DatePicker::make('fecha_de_aprobacion')
                ->label('Fecha de Aprobación')
                ->hidden(fn($livewire) => $livewire instanceof CreateRecord)
                ->nullable(),

            TextInput::make('monto_aprobado')
                ->label('Monto Aprobado')
                ->hidden(fn($livewire) => $livewire instanceof CreateRecord)
                ->nullable()
                ->numeric()
                
                ->step(0.01),

                Select::make('tasa_interes_id')
                    ->label('Tasa de Interés')
                    ->relationship(name: 'tasasInteres', titleAttribute: 'tasa_id', modifyQueryUsing:fn(Builder $query)=>$query->select('tasa_id')->where('destino','=','prestamo')) // Asegúrate de que la relación esté bien definida
                    
                    ->preload()
                    ->searchable()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Reporte Solicitudes de Prestamo')
           
            ->columns([
                //
                TextColumn::make('prestamo_id')->label('ID del Préstamo'),
                TextColumn::make('cliente.nombre')->label('Cliente')->searchable(), // Mostramos el nombre del cliente
                TextColumn::make('monto_solicitado')->label('Monto Solicitado')->money('HNL')
                ->badge()
                ->color(fn ($record) => match ($record->estado) {
                    'pendiente' => 'warning',  // Amarillo
                    'aprobado'  => 'success',  // Verde
                    'rechazado' => 'danger',   // Rojo
                    default => 'gray',         // Por si hay otros estados
                }),
                TextColumn::make('fecha_solicitud')->label('Fecha de Solicitud')->date(),
                TextColumn::make('plazo_meses')->label('Plazo'),
                TextColumn::make('estado')->label('Estado')  ->badge()
                ->color(fn ($record) => match ($record->estado) {
                    'pendiente' => 'warning',  // Amarillo
                    'aprobado'  => 'success',  // Verde
                    'rechazado' => 'danger',   // Rojo
                    default => 'gray',         // Por si hay otros estados
                }),
                TextColumn::make('fecha_de_aprobacion')->label('Fecha de Aprobación')->date(),
                TextColumn::make('monto_aprobado')->label('Monto Aprobado'),
                TextColumn::make('created_at')->label('Creado')->date(),
                TextColumn::make('updated_at')->label('Actualizado')->date(),
            ])
            ->filters([
                //
                Filter::make('cliente.nombre')->label('filtra por cliente'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('aprobar')
                ->label('Aprobar/Rechazar')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($record) => $record->estado === 'pendiente') // Solo visible si está en "Pendiente"
                ->form([
                    Wizard::make([
                        Step::make('Validar Información')
                            ->schema([
                                TextInput::make('cliente_nombre')
                                ->label('Cliente')
                                ->default(fn ($record) => $record->cliente->nombre)
                                ->disabled(),
                            TextInput::make('monto_solicitado')
                                ->label('Monto Solicitado')
                                ->default(fn ($record) => $record->monto_solicitado)
                                ->disabled(),
                            TextInput::make('fecha_solicitud')
                                ->label('Fecha de Solicitud')
                                ->default(fn ($record) => $record->fecha_solicitud)
                                ->disabled(),
                                TextInput::make('plazo_meses')
                                ->label('Plazo')
                                ->default(fn ($record) => $record->plazo_meses)
                                ->disabled(),
                            ]),
                            Step::make('Definir Aprobación')->schema([
                                Select::make('estado')
                                    ->label('Estado de Aprobación')
                                    ->options([
                                        'aprobado' => 'Aprobado',
                                        'rechazado' => 'Rechazado'
                                    ])
                                    ->required()
                                    ->reactive(),
                                TextInput::make('monto_aprobado')
                                    ->label('Monto Aprobado')
                                    ->numeric()
                                    ->step(0.01)
                                    ->hidden(fn ($get) => $get('estado') !== 'aprobado'),
                                Textarea::make('comentarios')
                                    ->label('Comentarios')
                                    ->nullable(),
                            ]),
                            
                    ]),
                ])
                ->action(function (array $data, Prestamo $record):void
                {
                    if ($data['estado'] === 'aprobado') {
                        $filePath='contratos/contrato_'.$record->cliente->nombre.'.pdf';

                        $record->update([
                            'estado' => $data['estado'],
                            'monto_aprobado' => $data['estado'] === 'aprobado' ? $data['monto_aprobado'] : null,
                            'comentarios'=>$data['comentarios'],
                            'fecha_de_aprobacion' => now(),
                            'path_contract'=>$filePath,
                        ]);

                        $pdf=Pdf::loadView('pdf.contrato_prestamo',['prestamo'=>$record]);

                       

                        Storage::disk('public')->put($filePath,$pdf->output());

                        Notification::make()
                        ->title('Prestamo actualizado correctamente')
                        ->success()
                        ->send();
                        }else{
                            $record->update([
                                'estado' => $data['estado'],
                                'comentarios' => $data['comentarios'],
                            ]);
                        }
                }),
               // ->successNotification(Notification::make()->title('Registro actualizado')),
               Tables\Actions\Action::make('ver_contrato')
                ->label('Ver Contrato')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->visible(fn ($record) => $record->estado === 'aprobado' && $record->path_contract)
                ->url(fn ($record) => Storage::url($record->path_contract), true), // true = abre en una nueva pestaña
                

                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ]);
            
    }

    public static function getRelations(): array
    {
        return [
            //
            //PagosRelationManagerRelationManager::class
            PlanPagosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrestamos::route('/'),
            'create' => Pages\CreatePrestamo::route('/create'),
            'edit' => Pages\EditPrestamo::route('/{record}/edit'),
        ];
    }
}

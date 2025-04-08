<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make()
                ->schema([
                    TextInput::make('name')
                    ->label('Nombre de Usuario')
                    ->required()
                    ->maxLength(255),

                    TextInput::make('email')
                    ->required()
                    ->label('Direccion de correo')
                    ->maxLength(255),

                    TextInput::make('password')
                    ->password()
                    ->required()
                    ->label('Clave')
                    ->maxLength(255),

                    Select::make('role')
                    ->label('Rol')
                    ->relationship('roles', 'name') // Asegura que 'roles' es la relación correcta en el modelo
                    ->preload()
                    ->searchable()
                    ->required()
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
               TextColumn::make('name'),
               TextColumn::make('email'),
               TextColumn::make('roles.name')
    ->label('Roles')
    ->badge() // Opcional: muestra los roles con un estilo de etiqueta
    ->separator(', ') // Para mostrar múltiples roles separados por comas
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

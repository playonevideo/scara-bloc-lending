<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaircaseResource\Pages;
use App\Filament\Resources\StaircaseResource\RelationManagers;
use App\Models\Staircase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StaircaseResource extends Resource
{
    protected static ?string $model = Staircase::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-4';

    protected static ?string $navigationGroup = 'Clădire';

    protected static ?string $modelLabel = 'scară';

    protected static ?string $pluralModelLabel = 'Scări';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('building_id')
                    ->label('Clădire')
                    ->relationship('building', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('name')
                    ->label('Nume')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nume')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('building.name')
                    ->label('Clădire')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('floors_count')
                    ->label('Etaje')
                    ->counts('floors'),
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
            RelationManagers\FloorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaircases::route('/'),
            'create' => Pages\CreateStaircase::route('/create'),
            'edit' => Pages\EditStaircase::route('/{record}/edit'),
        ];
    }
}

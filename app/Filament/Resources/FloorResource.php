<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FloorResource\Pages;
use App\Filament\Resources\FloorResource\RelationManagers;
use App\Models\Floor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FloorResource extends Resource
{
    protected static ?string $model = Floor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Clădire';

    protected static ?string $modelLabel = 'etaj';

    protected static ?string $pluralModelLabel = 'Etaje';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('staircase_id')
                    ->label('Scară')
                    ->relationship('staircase', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('number')
                    ->label('Număr etaj')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Etaj')
                    ->sortable(),
                Tables\Columns\TextColumn::make('staircase.name')
                    ->label('Scară')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apartments_count')
                    ->label('Apartamente')
                    ->counts('apartments'),
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
            RelationManagers\ApartmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFloors::route('/'),
            'create' => Pages\CreateFloor::route('/create'),
            'edit' => Pages\EditFloor::route('/{record}/edit'),
        ];
    }
}

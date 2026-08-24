<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApartmentResource\Pages;
use App\Filament\Resources\ApartmentResource\RelationManagers;
use App\Models\Apartment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApartmentResource extends Resource
{
    protected static ?string $model = Apartment::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Clădire';

    protected static ?string $modelLabel = 'apartament';

    protected static ?string $pluralModelLabel = 'Apartamente';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('floor_id')
                    ->label('Etaj')
                    ->relationship('floor', 'number')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->staircase->name.' · Etajul '.$record->number)
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('number')
                    ->label('Număr apartament')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Apartament')
                    ->sortable(),
                Tables\Columns\TextColumn::make('floor.number')
                    ->label('Etaj')
                    ->sortable(),
                Tables\Columns\TextColumn::make('floor.staircase.name')
                    ->label('Scară')
                    ->searchable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Locatari')
                    ->counts('users'),
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
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApartments::route('/'),
            'create' => Pages\CreateApartment::route('/create'),
            'edit' => Pages\EditApartment::route('/{record}/edit'),
        ];
    }
}

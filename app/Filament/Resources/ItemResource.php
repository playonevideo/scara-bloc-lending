<?php

namespace App\Filament\Resources;

use App\Enums\ObjectCondition;
use App\Enums\ObjectStatus;
use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Marketplace';

    protected static ?string $modelLabel = 'obiect';

    protected static ?string $pluralModelLabel = 'Obiecte';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalii')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titlu')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('owner_id')
                            ->label('Proprietar')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->label('Categorie')
                            ->relationship('category', 'name')
                            ->searchable(),
                        Forms\Components\Textarea::make('description')
                            ->label('Descriere')
                            ->rows(4),
                        Forms\Components\Select::make('condition')
                            ->label('Stare')
                            ->options(ObjectCondition::options())
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(ObjectStatus::options())
                            ->required(),
                        Forms\Components\TextInput::make('max_borrow_days')
                            ->label('Zile maxime de împrumut')
                            ->numeric()
                            ->required(),
                        Forms\Components\Toggle::make('requires_personal_handover')
                            ->label('Predare personală'),
                        Forms\Components\Toggle::make('can_leave_at_door')
                            ->label('Poate fi lăsat la ușă'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publicat'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titlu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Proprietar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categorie'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ObjectStatus $state) => match ($state) {
                        ObjectStatus::Available => 'success',
                        ObjectStatus::Reserved => 'warning',
                        ObjectStatus::Borrowed => 'info',
                        ObjectStatus::Inactive => 'gray',
                    }),
                Tables\Columns\TextColumn::make('loans_count')
                    ->label('Solicitări')
                    ->counts('loans'),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publicat')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(ObjectStatus::options()),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categorie')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publicat'),
            ])
            ->actions([
                Tables\Actions\Action::make('togglePublish')
                    ->label(fn (Item $record) => $record->is_published ? 'Ascunde' : 'Publică')
                    ->icon(fn (Item $record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->action(fn (Item $record) => $record->update(['is_published' => ! $record->is_published])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}

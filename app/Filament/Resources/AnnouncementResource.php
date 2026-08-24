<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Comunitate';

    protected static ?string $modelLabel = 'anunț';

    protected static ?string $pluralModelLabel = 'Anunțuri';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titlu')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('body')
                    ->label('Conținut')
                    ->required()
                    ->rows(5),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Publicat la')
                    ->default(now())
                    ->required(),
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
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Autor'),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicat')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('published_at', 'desc');
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}

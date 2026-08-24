<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Comunicare';

    protected static ?string $modelLabel = 'mesaj';

    protected static ?string $pluralModelLabel = 'Mesaje';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('conversation_id')
                    ->label('Conversație')
                    ->relationship('conversation', 'id')
                    ->required(),
                Forms\Components\Select::make('sender_id')
                    ->label('Expeditor')
                    ->relationship('sender', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Textarea::make('body')
                    ->label('Conținut')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('read_at')
                    ->label('Citit la'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('conversation.id')
                    ->label('Conversație')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Expeditor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('body')
                    ->label('Conținut')
                    ->limit(60),
                Tables\Columns\TextColumn::make('read_at')
                    ->label('Citit la')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creat')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}

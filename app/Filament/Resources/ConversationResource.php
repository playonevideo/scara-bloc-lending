<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Comunicare';

    protected static ?string $modelLabel = 'conversație';

    protected static ?string $pluralModelLabel = 'Conversații';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject')
                    ->label('Subiect')
                    ->maxLength(255),
                Forms\Components\Select::make('object_id')
                    ->label('Obiect')
                    ->relationship('object', 'title')
                    ->searchable(),
                Forms\Components\Select::make('loan_id')
                    ->label('Împrumut')
                    ->relationship('loan', 'id'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subiect')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('object.title')
                    ->label('Obiect')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan.id')
                    ->label('Împrumut')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creat')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListConversations::route('/'),
            'create' => Pages\CreateConversation::route('/create'),
            'edit' => Pages\EditConversation::route('/{record}/edit'),
        ];
    }
}

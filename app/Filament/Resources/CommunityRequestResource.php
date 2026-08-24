<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityRequestResource\Pages;
use App\Models\CommunityRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommunityRequestResource extends Resource
{
    protected static ?string $model = CommunityRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Comunitate';

    protected static ?string $modelLabel = 'cerere';

    protected static ?string $pluralModelLabel = 'Cereri comunitate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Locatar')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('category_id')
                    ->label('Categorie')
                    ->relationship('category', 'name'),
                Forms\Components\TextInput::make('title')
                    ->label('Titlu')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Descriere')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Deschisă',
                        'closed' => 'Închisă',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titlu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Locatar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categorie'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => $state === 'open' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'open' ? 'Deschisă' : 'Închisă'),
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
            'index' => Pages\ListCommunityRequests::route('/'),
            'create' => Pages\CreateCommunityRequest::route('/create'),
            'edit' => Pages\EditCommunityRequest::route('/{record}/edit'),
        ];
    }
}

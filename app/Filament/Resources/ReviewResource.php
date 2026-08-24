<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Comunitate';

    protected static ?string $modelLabel = 'recenzie';

    protected static ?string $pluralModelLabel = 'Recenzii';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('loan_id')
                    ->label('Împrumut')
                    ->relationship('loan', 'id')
                    ->required(),
                Forms\Components\Select::make('reviewer_id')
                    ->label('Evaluator')
                    ->relationship('reviewer', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('reviewee_id')
                    ->label('Evaluat')
                    ->relationship('reviewee', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('rating')
                    ->label('Notă (1–5)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5),
                Forms\Components\Textarea::make('comment')
                    ->label('Comentariu')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan.id')
                    ->label('Împrumut')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Evaluator')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reviewee.name')
                    ->label('Evaluat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Notă')
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}

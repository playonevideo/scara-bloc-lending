<?php

namespace App\Filament\Resources;

use App\Enums\LoanStatus;
use App\Filament\Resources\LoanResource\Pages;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Împrumuturi';

    protected static ?string $modelLabel = 'împrumut';

    protected static ?string $pluralModelLabel = 'Împrumuturi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('object_id')
                    ->label('Obiect')
                    ->relationship('object', 'title')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('borrower_id')
                    ->label('Solicitant')
                    ->relationship('borrower', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('lender_id')
                    ->label('Proprietar')
                    ->relationship('lender', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(LoanStatus::options())
                    ->required(),
                Forms\Components\DatePicker::make('starts_at')->label('De la'),
                Forms\Components\DatePicker::make('ends_at')->label('Până la'),
                Forms\Components\Textarea::make('message')->label('Mesaj')->rows(3),
                Forms\Components\Textarea::make('refused_reason')->label('Motiv refuz')->rows(2),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('object.title')
                    ->label('Obiect')
                    ->searchable(),
                Tables\Columns\TextColumn::make('borrower.name')
                    ->label('Solicitant')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lender.name')
                    ->label('Proprietar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Perioada')
                    ->date('d.m.Y'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (LoanStatus $state) => match ($state) {
                        LoanStatus::Requested => 'warning',
                        LoanStatus::Accepted => 'info',
                        LoanStatus::Borrowed => 'primary',
                        LoanStatus::Returned => 'success',
                        LoanStatus::Completed => 'success',
                        LoanStatus::Refused => 'danger',
                        LoanStatus::Cancelled => 'gray',
                        LoanStatus::Overdue => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creat')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(LoanStatus::options()),
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}

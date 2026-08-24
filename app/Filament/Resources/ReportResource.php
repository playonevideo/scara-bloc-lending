<?php

namespace App\Filament\Resources;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Moderare';

    protected static ?string $modelLabel = 'raportare';

    protected static ?string $pluralModelLabel = 'Raportări';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('reason')
                    ->label('Motiv')
                    ->options(ReportReason::options())
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(ReportStatus::options())
                    ->required(),
                Forms\Components\Textarea::make('details')
                    ->label('Detalii')
                    ->rows(3),
                Forms\Components\Textarea::make('resolution_note')
                    ->label('Notă rezoluție')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reporter.name')
                    ->label('Raportat de')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reportable_type')
                    ->label('Tip')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'object' => 'Obiect',
                        'message' => 'Mesaj',
                        'user' => 'Utilizator',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('reportable_id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Motiv')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ReportStatus $state) => match ($state) {
                        ReportStatus::New => 'danger',
                        ReportStatus::InReview => 'warning',
                        ReportStatus::Resolved => 'success',
                        ReportStatus::Dismissed => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creat')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(ReportStatus::options()),
            ])
            ->actions([
                Tables\Actions\Action::make('resolve')
                    ->label('Rezolvă')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Report $record) {
                        $record->update([
                            'status' => ReportStatus::Resolved,
                            'resolved_by' => auth()->id(),
                            'resolved_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('dismiss')
                    ->label('Respinge')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->action(function (Report $record) {
                        $record->update([
                            'status' => ReportStatus::Dismissed,
                            'resolved_by' => auth()->id(),
                            'resolved_at' => now(),
                        ]);
                    }),
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
            'index' => Pages\ListReports::route('/'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvitationResource\Pages;
use App\Models\Invitation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Comunitate';

    protected static ?string $modelLabel = 'invitație';

    protected static ?string $pluralModelLabel = 'Invitații';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->label('Email (opțional)')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefon (opțional)')
                    ->tel()
                    ->maxLength(32),
                Forms\Components\Select::make('apartment_id')
                    ->label('Apartament asociat')
                    ->relationship('apartment', 'number')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->fullLabel())
                    ->searchable()
                    ->preload(),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Expiră la')
                    ->default(now()->addDays(7))
                    ->required(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Cod')
                    ->badge()
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('apartment.number')
                    ->label('Apartament')
                    ->getStateUsing(fn ($record) => $record->apartment?->fullLabel() ?? '—'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expiră')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Stare')
                    ->badge()
                    ->color(fn (Invitation $record) => match (true) {
                        $record->isUsed() => 'gray',
                        $record->isExpired() => 'danger',
                        default => 'success',
                    })
                    ->state(fn (Invitation $record) => match (true) {
                        $record->isUsed() => 'Folosită',
                        $record->isExpired() => 'Expirată',
                        default => 'Activă',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('copyLink')
                    ->label('Link de înregistrare')
                    ->icon('heroicon-o-clipboard-document')
                    ->action(function (Invitation $record) {
                        $url = route('register', ['code' => $record->code]);

                        Notification::make()
                            ->title('Link copiat')
                            ->body($url)
                            ->success()
                            ->send();
                    })
                    ->extraAttributes(fn (Invitation $record) => [
                        'x-data' => '{}',
                        'x-on:click' => "navigator.clipboard.writeText('".route('register', ['code' => $record->code])."')",
                    ]),
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
            'index' => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitation::route('/create'),
            'edit' => Pages\EditInvitation::route('/{record}/edit'),
        ];
    }
}

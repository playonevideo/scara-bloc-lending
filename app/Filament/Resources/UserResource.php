<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Notifications\AccountSuspended;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Comunitate';

    protected static ?string $modelLabel = 'locatar';

    protected static ?string $pluralModelLabel = 'Locatari';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informații cont')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nume complet')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(32),
                        Forms\Components\TextInput::make('password')
                            ->label('Parolă')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->minLength(8)
                            ->revealable(),
                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options(Role::options())
                            ->default(Role::Resident->value)
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('Apartament')
                    ->schema([
                        Forms\Components\Select::make('apartment_id')
                            ->label('Apartament')
                            ->relationship('apartment', 'number')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->fullLabel())
                            ->searchable()
                            ->preload(),
                    ]),
                Forms\Components\Section::make('Stare cont')
                    ->schema([
                        Forms\Components\Toggle::make('is_blocked')
                            ->label('Cont blocat')
                            ->helperText('Un cont blocat nu se mai poate autentifica.'),
                        Forms\Components\Toggle::make('two_factor_enabled')
                            ->label('Autentificare în doi pași (SMS)'),
                        Forms\Components\Toggle::make('show_apartment')
                            ->label('Afișează numărul apartamentului')
                            ->default(true),
                        Forms\Components\Toggle::make('show_floor')
                            ->label('Afișează etajul')
                            ->default(true),
                        Forms\Components\Toggle::make('show_phone')
                            ->label('Afișează numărul de telefon'),
                        Forms\Components\Toggle::make('show_email')
                            ->label('Afișează email-ul'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nume')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (Role $state) => match ($state) {
                        Role::SuperAdmin => 'danger',
                        Role::Admin => 'warning',
                        Role::Resident => 'success',
                    }),
                Tables\Columns\TextColumn::make('apartment.fullLabel')
                    ->label('Apartament')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_blocked')
                    ->label('Blocat')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creat')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options(Role::options()),
                Tables\Filters\TernaryFilter::make('is_blocked')
                    ->label('Blocat'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggleBlock')
                    ->label(fn (User $record) => $record->is_blocked ? 'Deblochează' : 'Blochează')
                    ->icon(fn (User $record) => $record->is_blocked ? 'heroicon-o-check-circle' : 'heroicon-o-no-symbol')
                    ->color(fn (User $record) => $record->is_blocked ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update([
                            'is_blocked' => ! $record->is_blocked,
                            'blocked_at' => $record->is_blocked ? null : now(),
                        ]);

                        if ($record->is_blocked) {
                            $record->notify(new AccountSuspended);
                        }
                    }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('apartment.floor.staircase.building');
    }
}

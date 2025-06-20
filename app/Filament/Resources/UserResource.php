<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?string $navigationGroup = 'Пользователи и роли';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        $isAdmin = auth()->user()->hasRole('admin');
        
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255)
                            ->rules(['required', 'string', 'max:255']),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['required', 'email', 'max:255', 'unique:users,email']),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(255)
                            ->rules(['nullable', 'string', 'max:255']),
                        
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Аватар')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->rules(['nullable', 'image', 'max:2048']),
                    ])->columns(2),

                Section::make('Безопасность')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rules([
                                'required_if:context,create',
                                Password::defaults(),
                            ])
                            ->confirmed(),
                        
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Подтверждение пароля')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rules(['required_if:context,create']),
                        
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email подтвержден')
                            ->visible(fn () => $isAdmin),
                    ])->columns(2),

                Section::make('Дополнительная информация')
                    ->schema([
                        Forms\Components\TextInput::make('balance')
                            ->label('Баланс')
                            ->numeric()
                            ->prefix('₽')
                            ->rules(['nullable', 'numeric', 'min:0'])
                            ->visible(fn () => $isAdmin),
                        
                        Forms\Components\TextInput::make('group')
                            ->label('Группа')
                            ->maxLength(255)
                            ->rules(['nullable', 'string', 'max:255']),
                        
                        Forms\Components\Toggle::make('is_client')
                            ->label('Клиент')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('verification_code')
                            ->label('Код верификации')
                            ->maxLength(255)
                            ->rules(['nullable', 'string', 'max:255'])
                            ->visible(fn () => $isAdmin),
                    ])->columns(2),

                // Роли только для админов
                Section::make('Роли и права')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Роли')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->visible(fn () => $isAdmin),
                    ])
                    ->visible(fn () => $isAdmin),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Аватар')
                    ->circular()
                    ->size(40),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Роли')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('balance')
                    ->label('Баланс')
                    ->money('rub')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('is_client')
                    ->label('Клиент')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email подтвержден')
                    ->boolean()
                    ->getStateUsing(fn (User $record): bool => !is_null($record->email_verified_at))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата регистрации')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Роли')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('is_client')
                    ->label('Клиент'),
                
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email подтвержден'),
                
                Tables\Filters\Filter::make('balance_range')
                    ->label('Диапазон баланса')
                    ->form([
                        Forms\Components\TextInput::make('min_balance')
                            ->label('Мин. баланс')
                            ->numeric()
                            ->prefix('₽'),
                        Forms\Components\TextInput::make('max_balance')
                            ->label('Макс. баланс')
                            ->numeric()
                            ->prefix('₽'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_balance'],
                                fn (Builder $query, $balance): Builder => $query->where('balance', '>=', $balance),
                            )
                            ->when(
                                $data['max_balance'],
                                fn (Builder $query, $balance): Builder => $query->where('balance', '<=', $balance),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Просмотр'),
                Tables\Actions\EditAction::make()
                    ->label('Редактировать'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('admin')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Админы видят всех пользователей
        if (auth()->user()->hasRole('admin')) {
            return $query;
        }
        
        // Остальные видят только себя
        return $query->where('id', auth()->id());
    }
}

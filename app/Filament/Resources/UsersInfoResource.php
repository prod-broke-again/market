<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersInfoResource\Pages;
use App\Filament\Resources\UsersInfoResource\RelationManagers;
use App\Models\UsersInfo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UsersInfoResource extends Resource
{
    protected static ?string $model = UsersInfo::class;
    protected static ?string $modelLabel = 'Юр. информация';
    protected static ?string $pluralModelLabel = 'Юр. информация';
    protected static ?string $navigationGroup = 'Пользователи';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Аватар (логотип)')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->disk('public')
                            ->directory('avatars'),

                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->visible(fn () => $isAdmin),

                        Forms\Components\TextInput::make('legal_name')
                            ->label('Юридическое название')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('address_id')
                            ->label('Юридический адрес')
                            ->relationship(
                                'address',
                                'address',
                                modifyQueryUsing: fn (Builder $query) => $isAdmin ? $query : $query->where('user_id', $user->id)
                            )
                            ->searchable()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Реквизиты')
                    ->schema([
                        Forms\Components\TextInput::make('inn')
                            ->label('ИНН')
                            ->required()
                            ->maxLength(12),
                        Forms\Components\TextInput::make('kpp')
                            ->label('КПП')
                            ->maxLength(9),
                        Forms\Components\TextInput::make('ogrn')
                            ->label('ОГРН/ОГРНИП')
                            ->required()
                            ->maxLength(15),
                    ])->columns(3),

                Forms\Components\Section::make('Контакты')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('legal_name')
                    ->label('Юр. название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address.address')
                    ->label('Юр. адрес')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('inn')
                    ->label('ИНН')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ogrn')
                    ->label('ОГРН')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->hasRole('admin')),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!Auth::user()->hasRole('admin')) {
            return $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        if (Auth::user()->hasRole('admin')) {
            return true;
        }

        // Продавец может создать запись, только если у него ее еще нет
        return !UsersInfo::where('user_id', Auth::id())->exists();
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
            'index' => Pages\ListUsersInfos::route('/'),
            'create' => Pages\CreateUsersInfo::route('/create'),
            'view' => Pages\ViewUsersInfo::route('/{record}'),
            'edit' => Pages\EditUsersInfo::route('/{record}/edit'),
        ];
    }
}

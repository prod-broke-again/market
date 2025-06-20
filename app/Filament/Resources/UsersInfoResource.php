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

class UsersInfoResource extends Resource
{
    protected static ?string $model = UsersInfo::class;
    protected static ?string $modelLabel = 'Юр. информация';
    protected static ?string $pluralModelLabel = 'Юр. информация';
    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('legal_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('legal_address')
                    ->maxLength(2048),
                Forms\Components\TextInput::make('inn')
                    ->maxLength(255),
                Forms\Components\TextInput::make('kpp')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ogrn')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(2048),
                Forms\Components\TextInput::make('adress_ur')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('legal_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('legal_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kpp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ogrn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('adress_ur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('seller')) {
            return $query->where('user_id', auth()->id());
        }

        return $query;
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
            'edit' => Pages\EditUsersInfo::route('/{record}/edit'),
        ];
    }
}

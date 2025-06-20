<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopResource\Pages;
use App\Filament\Resources\ShopResource\RelationManagers;
use App\Models\Shop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;
    protected static ?string $modelLabel = 'Магазин';
    protected static ?string $pluralModelLabel = 'Магазины';
    protected static ?string $navigationGroup = 'Управление магазином';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Владелец')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Название магазина')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\MarkdownEditor::make('description')
                            ->label('Описание')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Контактная информация')
                    ->schema([
                        Forms\Components\Select::make('address_id')
                            ->label('Адрес')
                            ->relationship('address', 'address')
                            ->searchable(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->tel(),
                    ]),

                Forms\Components\Section::make('Время работы')
                    ->schema([
                        Forms\Components\TimePicker::make('work_times_start')
                            ->label('Начало работы')
                            ->seconds(false),
                        Forms\Components\TimePicker::make('work_times_end')
                            ->label('Окончание работы')
                            ->seconds(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Владелец')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address.address')
                    ->label('Адрес')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
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
            'index' => Pages\ListShops::route('/'),
            'create' => Pages\CreateShop::route('/create'),
            'edit' => Pages\EditShop::route('/{record}/edit'),
        ];
    }
}

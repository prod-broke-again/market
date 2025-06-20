<?php

namespace App\Filament\Resources;

use App\Enums\CustomerRequestStatus;
use App\Filament\Resources\CustomerRequestResource\Pages;
use App\Filament\Resources\CustomerRequestResource\RelationManagers;
use App\Models\CustomerRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerRequestResource extends Resource
{
    protected static ?string $model = CustomerRequest::class;
    protected static ?string $modelLabel = 'Заявка покупателя';
    protected static ?string $pluralModelLabel = 'Заявки покупателей';
    protected static ?string $navigationGroup = 'Заявки и отклики';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Детали заявки')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Покупатель')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Название/тема заявки')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('Категория')
                            ->relationship('category', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options(CustomerRequestStatus::class)
                            ->required(),
                        Forms\Components\MarkdownEditor::make('description')
                            ->label('Подробное описание')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Ценовые ожидания')
                    ->schema([
                        Forms\Components\TextInput::make('min_price')
                            ->label('Минимальная цена')
                            ->numeric()
                            ->prefix('₽'),
                        Forms\Components\TextInput::make('max_price')
                            ->label('Максимальная цена')
                            ->numeric()
                            ->prefix('₽'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Изображения')
                    ->schema([
                        Forms\Components\Repeater::make('images')
                            ->label('Ссылки на изображения')
                            ->simple(
                                Forms\Components\TextInput::make('url')->label('URL изображения')
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название заявки')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Покупатель')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->searchable(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerRequests::route('/'),
            'create' => Pages\CreateCustomerRequest::route('/create'),
            'edit' => Pages\EditCustomerRequest::route('/{record}/edit'),
        ];
    }
}

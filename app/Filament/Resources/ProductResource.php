<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $modelLabel = 'Товар';
    protected static ?string $pluralModelLabel = 'Товары';
    protected static ?string $navigationGroup = 'Управление магазином';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Основная информация')
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->label('Название товара')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL (слаг)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('product_description')
                                    ->label('Описание товара')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Изображения и цвета')
                            ->schema([
                                Forms\Components\Repeater::make('product_images')
                                    ->label('Изображения')
                                    ->simple(
                                        Forms\Components\TextInput::make('url')->label('URL изображения')
                                    ),
                                
                                Forms\Components\KeyValue::make('product_colors')
                                     ->label('Цвета')
                                     ->keyLabel('Название цвета (напр. "красный")')
                                     ->valueLabel('HEX-код (напр. "#FF0000")'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Цена и наличие')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Цена')
                                    ->numeric()
                                    ->prefix('₽')
                                    ->required(),
                                Forms\Components\TextInput::make('price_discount')
                                    ->label('Цена со скидкой')
                                    ->numeric()
                                    ->prefix('₽'),
                                Forms\Components\TextInput::make('count')
                                    ->label('Количество')
                                    ->numeric(),
                                Forms\Components\TextInput::make('articul')
                                    ->label('Артикул'),
                            ]),

                        Forms\Components\Section::make('Статус')
                            ->schema([
                                Forms\Components\Toggle::make('availability')
                                    ->label('В наличии')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_draft')
                                    ->label('Черновик'),
                            ]),
                        
                        Forms\Components\Section::make('Связи')
                            ->schema([
                                Forms\Components\Select::make('seller_id')
                                    ->label('Продавец')
                                    ->relationship('seller', 'name')
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('category_id')
                                    ->label('Категория')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Продавец')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Категория')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\IconColumn::make('availability')
                    ->label('В наличии')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ...
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (auth()->user()->hasRole('seller')) {
            return $query->where('seller_id', auth()->id());
        }
        
        return $query;
    }
}

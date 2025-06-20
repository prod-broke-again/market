<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static ?string $modelLabel = 'Отзыв';
    protected static ?string $pluralModelLabel = 'Отзывы';
    protected static ?string $navigationGroup = 'Отзывы и рейтинги';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function form(Form $form): Form
    {
        $isAdmin = auth()->user()->hasRole('admin');
        $isSeller = auth()->user()->hasRole('seller');
        
        return $form
            ->schema([
                Section::make('Информация об отзыве')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                    ->required()
                            ->rules(['required', 'exists:users,id'])
                            ->visible(fn () => $isAdmin),
                        
                        Forms\Components\Select::make('seller_id')
                            ->label('Продавец')
                            ->relationship('seller', 'name')
                            ->searchable()
                    ->required()
                            ->rules(['required', 'exists:users,id'])
                            ->visible(fn () => $isAdmin)
                            ->disabled(fn () => $isSeller),
                        
                        Forms\Components\Select::make('product_id')
                            ->label('Товар')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->rules(['nullable', 'exists:products,id'])
                            ->visible(fn () => $isAdmin),
                    ])->columns(2),

                Section::make('Оценка и комментарий')
                    ->schema([
                        Forms\Components\Select::make('rating')
                            ->label('Оценка')
                            ->options([
                                1 => '1 звезда - Очень плохо',
                                2 => '2 звезды - Плохо',
                                3 => '3 звезды - Удовлетворительно',
                                4 => '4 звезды - Хорошо',
                                5 => '5 звезд - Отлично',
                            ])
                    ->required()
                            ->rules(['required', 'integer', 'min:1', 'max:5'])
                            ->default(5),
                        
                        Forms\Components\Textarea::make('comment')
                            ->label('Комментарий')
                    ->required()
                            ->maxLength(1000)
                            ->rules(['required', 'string', 'min:10', 'max:1000'])
                            ->placeholder('Опишите ваши впечатления...')
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Продавец')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Товар')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Оценка')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('comment')
                    ->label('Комментарий')
                    ->limit(100)
                    ->wrap()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата отзыва')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Оценка')
                    ->options([
                        1 => '1 звезда',
                        2 => '2 звезды',
                        3 => '3 звезды',
                        4 => '4 звезды',
                        5 => '5 звезд',
                    ]),
                
                Tables\Filters\Filter::make('date_range')
                    ->label('Период')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('С'),
                        Forms\Components\DatePicker::make('until')
                            ->label('По'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Продавцы видят только отзывы о себе
        if (auth()->user()->hasRole('seller')) {
            return $query->where('seller_id', auth()->id());
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'view' => Pages\ViewReview::route('/{record}'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}

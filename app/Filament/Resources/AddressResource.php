<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddressResource\Pages;
use App\Filament\Resources\AddressResource\RelationManagers;
use App\Filament\Forms\Components\MapPicker;
use App\Models\Address;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;
    protected static ?string $modelLabel = 'Адрес';
    protected static ?string $pluralModelLabel = 'Адреса';
    protected static ?string $navigationGroup = 'Локации';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');
        
        // Определяем провайдера карт из конфигурации
        $mapProvider = config('maps.default_provider', 'google');
        
        return $form
            ->schema([
                Section::make('Выбор местоположения')
                    ->description('Используйте карту для выбора точного местоположения или введите адрес вручную')
                    ->schema([
                        MapPicker::make('coordinates')
                            ->label('Карта')
                            ->mapType($mapProvider) // Используем провайдера из конфигурации
                            ->defaultCoordinates(
                                config('maps.defaults.latitude', 55.7558),
                                config('maps.defaults.longitude', 37.6176)
                            )
                            ->zoom(config('maps.defaults.zoom', 12))
                            ->height(config('maps.defaults.height', '400px'))
                            ->columnSpanFull(),
                        
                        // Альтернативные поля для координат (если карта недоступна)
                        Forms\Components\TextInput::make('latitude')
                            ->label('Широта')
                            ->numeric()
                            ->rules(['nullable', 'numeric', 'between:-90,90'])
                            ->placeholder('55.7558')
                            ->visible(fn () => $isAdmin),
                        
                        Forms\Components\TextInput::make('longitude')
                            ->label('Долгота')
                            ->numeric()
                            ->rules(['nullable', 'numeric', 'between:-180,180'])
                            ->placeholder('37.6176')
                            ->visible(fn () => $isAdmin),
                    ])->columns(2),

                Section::make('Информация об адресе')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Полный адрес')
                            ->required()
                            ->maxLength(255)
                            ->rules(['required', 'string', 'max:255'])
                            ->placeholder('Введите полный адрес...')
                            ->helperText('Адрес будет автоматически заполнен при выборе на карте'),
                    ])->columns(1),

                Section::make('Связи')
                    ->description('Привяжите адрес к пользователю, магазину или товару.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->default(fn() => $isAdmin ? null : $user->id)
                            ->disabled(fn() => !$isAdmin)
                            ->visible(fn() => $isAdmin),
                        
                        Forms\Components\Select::make('shop_id')
                            ->label('Магазин')
                            ->relationship('shop', 'name', modifyQueryUsing: fn (Builder $query) => $isAdmin ? $query : $query->where('user_id', $user->id))
                            ->searchable()
                            ->rules(['nullable', 'exists:shops,id'])
                            ->visible(fn () => $isAdmin || $user->hasRole('seller')),

                        Forms\Components\Select::make('product_id')
                            ->label('Товар')
                            ->relationship('product', 'name', modifyQueryUsing: fn (Builder $query) => $isAdmin ? $query : $query->whereHas('shop', fn($q) => $q->where('user_id', $user->id)))
                            ->searchable()
                            ->rules(['nullable', 'exists:products,id'])
                            ->visible(fn () => $isAdmin || $user->hasRole('seller')),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('address')
                    ->label('Адрес')
                    ->searchable()
                    ->wrap()
                    ->limit(100),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Товар')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('shop.name')
                    ->label('Магазин')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Координаты')
                    ->formatStateUsing(function (Address $record): string {
                        if ($record->latitude && $record->longitude) {
                            return "{$record->latitude}, {$record->longitude}";
                        }
                        return 'Не указаны';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_coordinates')
                    ->label('Только с координатами')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('latitude')->whereNotNull('longitude')),
                
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

        // Продавцы видят только свои адреса
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
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'view' => Pages\ViewAddress::route('/{record}'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }
}

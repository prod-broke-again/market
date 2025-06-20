<?php

namespace App\Filament\Resources;

use App\Enums\ResponseStatus;
use App\Filament\Resources\ResponseResource\Pages;
use App\Filament\Resources\ResponseResource\RelationManagers\MessagesRelationManager;
use App\Models\Response;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Min;

class ResponseResource extends Resource
{
    protected static ?string $model = Response::class;
    protected static ?string $modelLabel = 'Отклик продавца';
    protected static ?string $pluralModelLabel = 'Отклики продавцов';
    protected static ?string $navigationGroup = 'Заявки и отклики';
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function form(Form $form): Form
    {
        $isAdmin = auth()->user()->hasRole('admin');
        $isSeller = auth()->user()->hasRole('seller');

        return $form
            ->schema([
                Section::make('Детали отклика')
                    ->schema([
                        Forms\Components\Select::make('customer_request_id')
                            ->label('Заявка покупателя')
                            ->relationship('customerRequest', 'name')
                            ->searchable()
                            ->required()
                            ->rules(['required', 'exists:customer_requests,id'])
                            ->disabled(fn () => !$isAdmin && !$isSeller),
                        
                        // Показываем выбор продавца только админам
                        Forms\Components\Select::make('seller_id')
                            ->label('Продавец')
                            ->relationship('seller', 'name')
                            ->searchable()
                            ->required()
                            ->rules(['required', 'exists:users,id'])
                            ->visible(fn () => $isAdmin)
                            ->disabled(fn () => !$isAdmin),
                    ])->columns(2),

                Section::make('Предложение продавца')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Предложенная цена')
                            ->numeric()
                            ->prefix('₽')
                            ->rules([
                                'required',
                                'numeric',
                                new Min(0.01, 'Цена должна быть больше 0')
                            ])
                            ->disabled(fn () => !$isAdmin && !$isSeller),
                        
                        // Статус могут менять только админы
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options(ResponseStatus::class)
                            ->required()
                            ->rules(['required', 'in:' . implode(',', array_column(ResponseStatus::cases(), 'value'))])
                            ->visible(fn () => $isAdmin)
                            ->disabled(fn () => !$isAdmin),
                        
                        Forms\Components\MarkdownEditor::make('description')
                            ->label('Комментарий')
                            ->rules(['required', 'string', 'min:10', 'max:2000'])
                            ->columnSpanFull()
                            ->disabled(fn () => !$isAdmin && !$isSeller),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = auth()->user()->hasRole('admin');
        
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customerRequest.name')
                    ->label('Заявка')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Продавец')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('rub')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'awaiting_confirmation' => 'warning',
                        'completed' => 'info',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(ResponseStatus::class),
                Tables\Filters\Filter::make('price_range')
                    ->label('Диапазон цен')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->label('Мин. цена')
                            ->numeric()
                            ->prefix('₽'),
                        Forms\Components\TextInput::make('max_price')
                            ->label('Макс. цена')
                            ->numeric()
                            ->prefix('₽'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['max_price'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Действия только для админов
                Action::make('markAsCompleted')
                    ->label('Запрос выполнен')
                    ->action(function (Response $record) {
                        $record->status = ResponseStatus::Completed;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Response $record) => $isAdmin && $record->status !== ResponseStatus::Completed),
                
                Action::make('markAsRejected')
                    ->label('Отклонить')
                    ->action(function (Response $record) {
                        $record->status = ResponseStatus::Rejected;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Response $record) => $isAdmin && $record->status === ResponseStatus::Active),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => $isAdmin),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResponses::route('/'),
            'create' => Pages\CreateResponse::route('/create'),
            'edit' => Pages\EditResponse::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Продавцы видят только свои отклики
        if (auth()->user()->hasRole('seller')) {
            return $query->where('seller_id', auth()->id());
        }
        
        return $query;
    }
}

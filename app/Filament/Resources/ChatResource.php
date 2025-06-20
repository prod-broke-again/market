<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatResource\Pages;
use App\Filament\Resources\ChatResource\RelationManagers;
use App\Models\Chat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChatResource extends Resource
{
    protected static ?string $model = Chat::class;
    protected static ?string $modelLabel = 'Чат';
    protected static ?string $pluralModelLabel = 'Чаты';
    protected static ?string $navigationGroup = 'Коммуникации';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['admin', 'super_admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Информация о чате')
                    ->schema([
                        Forms\Components\Select::make('response_id')
                            ->label('Отклик')
                            ->relationship('response', 'id')
                            ->searchable()
                    ->required()
                            ->rules(['required', 'exists:responses,id']),
                        
                        Forms\Components\Select::make('seller_id')
                            ->label('Продавец')
                            ->relationship('seller', 'name')
                            ->searchable()
                    ->required()
                            ->rules(['required', 'exists:users,id']),
                        
                        Forms\Components\Select::make('customer_id')
                            ->label('Покупатель')
                            ->relationship('customer', 'name')
                            ->searchable()
                    ->required()
                            ->rules(['required', 'exists:users,id']),
                        
                        Forms\Components\Select::make('status')
                            ->label('Статус чата')
                            ->options([
                                'active' => 'Активный',
                                'closed' => 'Закрыт',
                                'archived' => 'Архивирован'
                            ])
                            ->default('active')
                    ->required()
                            ->rules(['required', 'in:active,closed,archived']),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('response.customerRequest.name')
                    ->label('Заявка')
                    ->searchable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Продавец')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Покупатель')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'closed' => 'danger',
                        'archived' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Сообщений')
                    ->counts('messages')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('last_message_at')
                    ->label('Последнее сообщение')
                    ->getStateUsing(function (Chat $record) {
                        return $record->messages()->latest()->first()?->created_at;
                    })
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активный',
                        'closed' => 'Закрыт',
                        'archived' => 'Архивирован'
                    ]),
                Tables\Filters\Filter::make('has_messages')
                    ->label('Только с сообщениями')
                    ->query(fn (Builder $query): Builder => $query->has('messages')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Просмотр чата'),
                Tables\Actions\EditAction::make()
                    ->label('Редактировать'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('super_admin')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChats::route('/'),
            'create' => Pages\CreateChat::route('/create'),
            'view' => Pages\ViewChat::route('/{record}'),
            'edit' => Pages\EditChat::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Админы видят все чаты, но можно добавить фильтрацию по необходимости
        return $query;
    }
}

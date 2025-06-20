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
        return $form
            ->schema([
                Section::make('Детали отклика')
                    ->schema([
                        Forms\Components\Select::make('customer_request_id')
                            ->label('Заявка покупателя')
                            ->relationship('customerRequest', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('seller_id')
                            ->label('Продавец')
                            ->relationship('seller', 'name')
                            ->searchable()
                            ->required(),
                    ])->columns(2),

                Section::make('Предложение продавца')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Предложенная цена')
                            ->numeric()
                            ->prefix('₽'),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options(ResponseStatus::class)
                            ->required(),
                        Forms\Components\MarkdownEditor::make('description')
                            ->label('Комментарий')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
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
                Action::make('markAsCompleted')
                    ->label('Запрос выполнен')
                    ->action(function (Response $record) {
                        $record->status = ResponseStatus::Completed;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Response $record) => $record->status !== ResponseStatus::Completed),
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
        
        if (auth()->user()->hasRole('seller')) {
            return $query->where('seller_id', auth()->id());
        }
        
        return $query;
    }
}

<?php

namespace App\Filament\Resources\ChatResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';
    protected static ?string $title = 'Сообщения чата';
    protected static ?string $modelLabel = 'Сообщение';
    protected static ?string $pluralModelLabel = 'Сообщения';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('message')
                    ->label('Текст сообщения')
                    ->required()
                    ->rules(['required', 'string', 'min:1', 'max:1000'])
                    ->maxLength(1000)
                    ->placeholder('Введите текст сообщения...')
                    ->columnSpanFull(),
                
                Forms\Components\Toggle::make('is_read')
                    ->label('Прочитано')
                    ->default(false),
                
                Forms\Components\Toggle::make('is_show')
                    ->label('Показано')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Автор')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('message')
                    ->label('Сообщение')
                    ->wrap()
                    ->limit(100)
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Прочитано')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_show')
                    ->label('Показано')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата отправки')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Прочитано'),
                Tables\Filters\TernaryFilter::make('is_show')
                    ->label('Показано'),
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Отправить сообщение')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    })
                    ->after(function () {
                        // Можно добавить уведомление
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Редактировать'),
                Tables\Actions\DeleteAction::make()
                    ->label('Удалить')
                    ->visible(fn () => auth()->user()->hasRole('admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('admin')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
} 
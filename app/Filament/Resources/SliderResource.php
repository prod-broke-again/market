<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Слайды';
    protected static ?string $navigationGroup = 'Контент';
    protected static ?string $modelLabel = 'Слайд';
    protected static ?string $pluralModelLabel = 'Слайды';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Изображение')
                    ->image()
                    ->imagePreviewHeight('150')
                    ->required(),
                Forms\Components\TextInput::make('url')
                    ->label('Ссылка')
                    ->placeholder('https://...')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('width')
                    ->label('Ширина (px)')
                    ->numeric()
                    ->minValue(100)
                    ->maxValue(2000),
                Forms\Components\TextInput::make('height')
                    ->label('Высота (px)')
                    ->numeric()
                    ->minValue(50)
                    ->maxValue(1200),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Изображение')
                    ->height(60),
                Tables\Columns\TextColumn::make('url')
                    ->label('Ссылка')
                    ->searchable(),
                Tables\Columns\TextColumn::make('width')
                    ->label('Ширина')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->label('Высота')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}

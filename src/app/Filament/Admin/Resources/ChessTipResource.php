<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ChessTipResource\Pages;
use App\Models\ChessTip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChessTipResource extends Resource
{
    protected static ?string $model = ChessTip::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Puzzles & Tips';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Chess Tips';

    protected static ?string $modelLabel = 'Chess Tip';

    protected static ?string $pluralModelLabel = 'Chess Tips';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('tip')
                    ->required()
                    ->columnSpanFull()
                    ->placeholder('Enter the chess tip or quote...'),

                Forms\Components\TextInput::make('author')
                    ->maxLength(255)
                    ->placeholder('e.g., Garry Kasparov')
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tip')
                    ->limit(80)
                    ->searchable(),
                Tables\Columns\TextColumn::make('author')
                    ->searchable()
                    ->default('Anonymous'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChessTips::route('/'),
            'create' => Pages\CreateChessTip::route('/create'),
            'edit' => Pages\EditChessTip::route('/{record}/edit'),
        ];
    }
}

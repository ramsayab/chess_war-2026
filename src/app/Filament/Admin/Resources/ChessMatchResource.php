<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ChessMatchResource\Pages;
use App\Models\ChessMatch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChessMatchResource extends Resource
{
    protected static ?string $model = ChessMatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Game Management';

    protected static ?int $navigationSort = -1;

    protected static ?string $navigationLabel = 'Matches';

    protected static ?string $modelLabel = 'Match';

    protected static ?string $pluralModelLabel = 'Matches';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->options(\App\Models\User::pluck('name', 'id')->toArray())
                    ->required()
                    ->label('Player'),

                Forms\Components\Toggle::make('is_win')
                    ->label('Is Win?')
                    ->default(false),

                Forms\Components\TextInput::make('total_time')
                    ->numeric()
                    ->required()
                    ->label('Total Time (Seconds)')
                    ->default(0),

                Forms\Components\Select::make('power_type')
                    ->label('Power Type')
                    ->options([
                        'confused_pawn' => 'Confused Pawn',
                        'blink_knight' => 'Blink Knight',
                        'grey_bishop' => 'Grey Bishop',
                        'super_rook' => 'Super Rook',
                        'omni_queen' => 'Omni Queen',
                        'undying_king' => 'Undying King',
                    ])
                    ->placeholder('Select a power type')
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Player')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_win')
                    ->boolean()
                    ->label('Win')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_time')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => floor($state / 60) . 'm ' . ($state % 60) . 's')
                    ->sortable(),
                Tables\Columns\TextColumn::make('power_type')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListChessMatches::route('/'),
            'create' => Pages\CreateChessMatch::route('/create'),
            'edit' => Pages\EditChessMatch::route('/{record}/edit'),
        ];
    }
}

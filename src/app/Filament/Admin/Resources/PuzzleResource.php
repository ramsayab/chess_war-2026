<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PuzzleResource\Pages;
use App\Filament\Admin\Resources\PuzzleResource\RelationManagers;
use App\Models\Puzzle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PuzzleResource extends Resource
{
    protected static ?string $model = Puzzle::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('difficulty')
                    ->options([
                        'easy' => 'Easy',
                        'medium' => 'Medium',
                        'hard' => 'Hard',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('diff_label')
                    ->label('Difficulty Label')
                    ->placeholder('e.g. Mate in 1')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('side_to_move')
                    ->options([
                        'white' => 'White',
                        'black' => 'Black',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('fen')
                    ->label('FEN String')
                    ->placeholder('e.g. r1bqkb1r/pppp1ppp/2n2n2/4p2Q/2B1P3/8/PPPP1PPP/RNB1K1NR w KQkq - 4 4')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->placeholder('e.g. White to move. Deliver checkmate in 1.')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('moves_limit')
                    ->label('Moves Limit')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\Repeater::make('solution')
                    ->simple(
                        Forms\Components\TextInput::make('move')
                            ->placeholder('e.g. h5f7')
                            ->required()
                    )
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('difficulty')
                    ->searchable(),
                Tables\Columns\TextColumn::make('diff_label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('side_to_move')
                    ->searchable(),
                Tables\Columns\TextColumn::make('moves_limit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListPuzzles::route('/'),
            'create' => Pages\CreatePuzzle::route('/create'),
            'edit' => Pages\EditPuzzle::route('/{record}/edit'),
        ];
    }
}

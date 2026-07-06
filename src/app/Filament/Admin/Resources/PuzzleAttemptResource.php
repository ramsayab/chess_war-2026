<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PuzzleAttemptResource\Pages;
use App\Models\PuzzleAttempt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PuzzleAttemptResource extends Resource
{
    protected static ?string $model = PuzzleAttempt::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Puzzles & Tips';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Puzzle Attempts';

    protected static ?string $modelLabel = 'Puzzle Attempt';

    protected static ?string $pluralModelLabel = 'Puzzle Attempts';

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

                Forms\Components\Select::make('puzzle_id')
                    ->options(\App\Models\Puzzle::pluck('name', 'id')->toArray())
                    ->required()
                    ->label('Puzzle'),

                Forms\Components\Toggle::make('solved')
                    ->label('Solved?')
                    ->default(false),

                Forms\Components\TextInput::make('attempts')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->label('Attempts Count'),

                Forms\Components\DateTimePicker::make('solved_at')
                    ->label('Solved At')
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
                Tables\Columns\TextColumn::make('puzzle.name')
                    ->label('Puzzle')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('solved')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attempts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('solved_at')
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
            'index' => Pages\ListPuzzleAttempts::route('/'),
            'create' => Pages\CreatePuzzleAttempt::route('/create'),
            'edit' => Pages\EditPuzzleAttempt::route('/{record}/edit'),
        ];
    }
}

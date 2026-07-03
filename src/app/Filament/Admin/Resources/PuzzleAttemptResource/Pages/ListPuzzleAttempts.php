<?php

namespace App\Filament\Admin\Resources\PuzzleAttemptResource\Pages;

use App\Filament\Admin\Resources\PuzzleAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPuzzleAttempts extends ListRecords
{
    protected static string $resource = PuzzleAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

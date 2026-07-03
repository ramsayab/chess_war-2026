<?php

namespace App\Filament\Admin\Resources\PuzzleAttemptResource\Pages;

use App\Filament\Admin\Resources\PuzzleAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPuzzleAttempt extends EditRecord
{
    protected static string $resource = PuzzleAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

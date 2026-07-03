<?php

namespace App\Filament\Admin\Resources\SavedGameResource\Pages;

use App\Filament\Admin\Resources\SavedGameResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSavedGames extends ListRecords
{
    protected static string $resource = SavedGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

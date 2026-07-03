<?php

namespace App\Filament\Admin\Resources\ChessTipResource\Pages;

use App\Filament\Admin\Resources\ChessTipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChessTip extends EditRecord
{
    protected static string $resource = ChessTipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources;

use Z3d0X\FilamentLogger\Resources\ActivityResource as BaseActivityResource;

class ActivityResource extends BaseActivityResource
{
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

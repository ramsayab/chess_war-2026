<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SavedGame;
use Illuminate\Auth\Access\HandlesAuthorization;

class SavedGamePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function view(User $user, SavedGame $savedGame): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function update(User $user, SavedGame $savedGame): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function delete(User $user, SavedGame $savedGame): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function deleteAny(User $user): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }
}

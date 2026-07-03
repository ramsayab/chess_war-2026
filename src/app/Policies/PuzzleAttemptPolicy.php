<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PuzzleAttempt;
use Illuminate\Auth\Access\HandlesAuthorization;

class PuzzleAttemptPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function view(User $user, PuzzleAttempt $puzzleAttempt): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function update(User $user, PuzzleAttempt $puzzleAttempt): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function delete(User $user, PuzzleAttempt $puzzleAttempt): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function deleteAny(User $user): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }
}

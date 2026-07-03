<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ChessTip;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChessTipPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function view(User $user, ChessTip $chessTip): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function update(User $user, ChessTip $chessTip): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function delete(User $user, ChessTip $chessTip): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function deleteAny(User $user): bool
    {
        return (bool) $user->is_admin || $user->hasAnyRole(['admin', 'super_admin']);
    }
}

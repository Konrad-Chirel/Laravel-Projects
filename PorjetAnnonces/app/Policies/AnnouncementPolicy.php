<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Announcement $announcement)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->type === 'enterprise';
    }

    public function update(User $user, Announcement $announcement)
    {
        return $user->type === 'enterprise' && $user->id === $announcement->user_id;
    }

    public function delete(User $user, Announcement $announcement)
    {
        return ($user->type === 'enterprise' && $user->id === $announcement->user_id) || 
               $user->type === 'admin';
    }

    public function validate(User $user, Announcement $announcement)
    {
        return $user->type === 'admin';
    }
}

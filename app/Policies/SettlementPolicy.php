<?php

namespace App\Policies;

use App\Models\Settlement;
use App\Models\User;

class SettlementPolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, Settlement $settlement): bool { return true; }
    public function create(User $user): bool   { return $user->isManager(); }
    public function update(User $user, Settlement $settlement): bool
    {
        return $user->isAdmin() && $settlement->status !== 'completed';
    }
    public function delete(User $user, Settlement $settlement): bool { return false; }
}

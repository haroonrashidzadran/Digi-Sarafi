<?php

namespace App\Policies;

use App\Models\Exchange;
use App\Models\User;

class ExchangePolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, Exchange $exchange): bool { return true; }
    public function create(User $user): bool   { return $user->isCashier(); }
    public function update(User $user, Exchange $exchange): bool
    {
        return $user->isManager() && $exchange->status === 'pending';
    }
    public function delete(User $user, Exchange $exchange): bool { return false; }
}

<?php

namespace App\Policies;

use App\Models\ExchangeRate;
use App\Models\User;

class ExchangeRatePolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, ExchangeRate $rate): bool { return true; }
    public function create(User $user): bool  { return $user->isAdmin(); }
    public function update(User $user, ExchangeRate $rate): bool { return $user->isManager(); }
    public function delete(User $user, ExchangeRate $rate): bool { return $user->isAdmin(); }
}

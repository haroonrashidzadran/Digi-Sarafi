<?php

namespace App\Policies;

use App\Models\CashSession;
use App\Models\User;

class CashSessionPolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, CashSession $session): bool { return true; }
    public function create(User $user): bool  { return $user->isCashier(); }
    public function update(User $user, CashSession $session): bool { return $user->isManager() && $session->status === 'open'; }
    public function delete(User $user, CashSession $session): bool { return false; }
}

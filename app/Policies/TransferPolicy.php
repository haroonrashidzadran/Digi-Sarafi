<?php

namespace App\Policies;

use App\Models\Transfer;
use App\Models\User;

class TransferPolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, Transfer $transfer): bool { return true; }
    public function create(User $user): bool   { return $user->isCashier(); }
    public function update(User $user, Transfer $transfer): bool
    {
        return $user->isManager() && !in_array($transfer->status, ['settled', 'cancelled']);
    }
    public function delete(User $user, Transfer $transfer): bool { return false; }
    public function approve(User $user, Transfer $transfer): bool { return $user->isManager(); }
    public function markPaid(User $user, Transfer $transfer): bool { return $user->isCashier(); }
    public function cancel(User $user, Transfer $transfer): bool  { return $user->isManager(); }
}

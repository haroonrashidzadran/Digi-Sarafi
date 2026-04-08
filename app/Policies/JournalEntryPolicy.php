<?php

namespace App\Policies;

use App\Models\JournalEntry;
use App\Models\User;

class JournalEntryPolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, JournalEntry $entry): bool { return true; }
    public function create(User $user): bool   { return $user->isManager(); }
    public function update(User $user, JournalEntry $entry): bool
    {
        return $user->isManager() && $entry->status === 'draft';
    }
    public function delete(User $user, JournalEntry $entry): bool { return false; }
    public function approve(User $user, JournalEntry $entry): bool { return $user->isManager(); }
    public function reverse(User $user, JournalEntry $entry): bool { return $user->isAdmin(); }
}

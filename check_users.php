<?php
$roles = ['cid_chief', 'sgod_chief', 'ao', 'asds', 'sds'];
$users = App\Models\User::whereIn('role', $roles)->get();

if ($users->isEmpty()) {
    echo "No users found with roles: " . implode(', ', $roles) . "\n";
} else {
    foreach ($users as $user) {
        echo "Found: {$user->full_name} ({$user->role}) - ID: {$user->id}\n";
    }
}

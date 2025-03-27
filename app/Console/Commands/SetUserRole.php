<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetUserRole extends Command
{
    protected $signature = 'user:role {email} {role=admin}';
    protected $description = 'Set a user\'s role by email';

    public function handle(): int
    {
        $email = $this->argument('email');
        $role = $this->argument('role');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $user->role = $role;
        $user->save();

        $this->info("User {$email} has been assigned the role: {$role}");
        return 0;
    }
}
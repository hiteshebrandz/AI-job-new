<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetUserRolePassword extends Command
{
    protected $signature = 'users:set-candidate-password {password=password : The plain password to set}';

    protected $description = 'Set the same login password for all users with role "user" (candidates)';

    public function handle(): int
    {
        $plain = (string) $this->argument('password');
        $hashed = Hash::make($plain);

        $count = User::query()
            ->where('role', User::ROLE_USER)
            ->update(['password' => $hashed]);

        $this->info("Updated {$count} candidate account(s). Login password is now: {$plain}");

        return self::SUCCESS;
    }
}

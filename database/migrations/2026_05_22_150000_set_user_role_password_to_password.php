<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Set login password to "password" for all candidate (role=user) accounts.
     * For local/demo use only — do not run on production with real user data.
     */
    public function up(): void
    {
        $hashed = Hash::make('password');

        User::query()
            ->where('role', User::ROLE_USER)
            ->update(['password' => $hashed]);
    }

    public function down(): void
    {
        // Cannot restore previous passwords.
    }
};

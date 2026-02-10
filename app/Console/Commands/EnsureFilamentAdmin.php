<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsureFilamentAdmin extends Command
{
    protected $signature = 'app:ensure-filament-admin';

    protected $description = 'Create or update the Filament admin user from environment variables.';

    public function handle(): int
    {
        $email = env('FILAMENT_ADMIN_EMAIL');
        $password = env('FILAMENT_ADMIN_PASSWORD');
        $name = env('FILAMENT_ADMIN_NAME', 'Admin');

        if (!$email || !$password) {
            $this->info('FILAMENT_ADMIN_EMAIL or FILAMENT_ADMIN_PASSWORD is missing.');
            return self::SUCCESS;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ]
        );

        $this->info("Filament admin user ensured for {$email}.");

        return self::SUCCESS;
    }
}
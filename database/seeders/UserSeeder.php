<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure primary organization exists
        $org = Organization::firstOrCreate(
            ['slug' => 'primary'],
            [
                'name'      => config('app.company', 'PDPA Studio'),
                'tax_id'    => '0000000000000',
                'industry'  => 'technology',
                'plan'      => 'enterprise',
                'status'    => 'active',
                'max_users' => 999,
            ]
        );
        $orgId = $org->id;

        foreach (config('accounts.users') as $account) {
            // Skip if email already exists
            if (User::where('email', $account['email'])->exists()) {
                $this->command->line("  ⏭  Skipped (exists): {$account['email']}");
                continue;
            }

            User::create([
                'organization_id'    => $orgId,
                'name'               => $account['name'],
                'email'              => $account['email'],
                'password'           => Hash::make($account['password']),
                'role'               => $account['role'],
                'status'             => 'active',
                'email_verified_at'  => now(),
                'last_login_at'      => null,
            ]);

            $this->command->line("  ✅  Created [{$account['role']}]: {$account['email']}");
        }

        $this->command->info('');
        $this->command->info('✅ UserSeeder done — accounts loaded from config/accounts.php');
        $this->command->line('');
        $this->command->line('   🔑 Admin logins:');
        $this->command->line('      admin@pdpa.local      → ' . env('SEED_PASSWORD_ADMIN',    'Admin@2025!'));
        $this->command->line('      nuntapol@pdpa.local   → ' . env('SEED_PASSWORD_NUNTAPOL', 'Nuntapol@2025!'));
        $this->command->line('   🔑 Other accounts:');
        $this->command->line('      editor01-03 / dpo01-03 / reviewer01-04  → ' . env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'));
    }
}

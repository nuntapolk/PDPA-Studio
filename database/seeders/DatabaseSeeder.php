<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,
            UserSeeder::class,
            DataSubjectSeeder::class,
            ConsentSeeder::class,
            RightsRequestSeeder::class,
            RopaSeeder::class,
            BreachSeeder::class,
            PrivacyNoticeSeeder::class,
            DpoSeeder::class,
            VendorSeeder::class,
            AssessmentSeeder::class,
            TrainingSeeder::class,
            ExternalPartySeeder::class,
            LogSeeder::class,
            SystemSeeder::class,
        ]);
    }
}

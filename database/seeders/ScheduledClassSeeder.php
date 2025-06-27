<?php

namespace Database\Seeders;

use App\Models\ScheduledClass;
use Illuminate\Database\Seeder;

class ScheduledClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Completed classes.
        ScheduledClass::factory()
            ->count(50)
            ->state(['status' => 'completed'])
            ->create();
        // Future classes where some are canceled.
        ScheduledClass::factory()
            ->count(50)
            ->create();
    }
}

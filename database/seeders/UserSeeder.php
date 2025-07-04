<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fixed user data for manual testing.
        User::factory()->create([
            'name' => 'Foo',
            'email' => 'foo@example.com',
        ]);
        User::factory()->create([
            'name' => 'Bar',
            'email' => 'bar@example.com',
        ]);
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'instructor',
        ]);
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // Random user data.
        User::factory()->count(10)->create();

        User::factory()->count(10)->create([
            'role' => 'instructor',
        ]);
    }
}

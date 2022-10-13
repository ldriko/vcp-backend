<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        User::factory()->createMany([
            ['name' => 'Heaven', 'email' => 'heaven@example.com'],
            ['name' => 'Cinta', 'email' => 'cinta@example.com'],
            ['name' => 'Reyza', 'email' => 'reyza@example.com'],
        ]);

        $this->call(CategorySeeder::class);
        $this->call(JournalSeeder::class);
        $this->call(GroupSeeder::class);
    }
}

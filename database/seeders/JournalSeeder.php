<?php

namespace Database\Seeders;

use App\Models\Journal;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Journal::factory()->create(['title' => 'Arsitektur IBM System/370', 'user_id' => 1]);
        Journal::factory()->create(['title' => 'Desain Terasering', 'user_id' => 1]);
        Journal::factory()->create(['title' => 'Diagnosis Modern GERD', 'user_id' => 2]);
        Journal::factory()->create(['title' => 'Tesla: Kendaraan Tenaga Listrik', 'user_id' => 2]);
        Journal::factory()->create(['title' => 'Pengaruh Pemanasan Global Pada Kehidupan Laut', 'user_id' => 3]);
        Journal::factory()->create(['title' => 'Implementasi Teknologi Blockchain Pada Pendidikan', 'user_id' => 3]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\JournalCategory;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $journals = [
            ['title' => 'Arsitektur IBM System/370', 'user_id' => 1, 'categories' => [7, 8]],
            ['title' => 'Desain Terasering', 'user_id' => 1, 'categories' => [1, 9, 10]],
            ['title' => 'Diagnosis Modern GERD', 'user_id' => 2, 'categories' => [7, 9]],
            ['title' => 'Tesla: Kendaraan Tenaga Listrik', 'user_id' => 2, 'categories' => [7, 8, 9, 10]],
            ['title' => 'Pengaruh Pemanasan Global Pada Kehidupan Laut', 'user_id' => 3, 'categories' => [9]],
            ['title' => 'Implementasi Teknologi Blockchain Pada Pendidikan', 'user_id' => 3, 'categories' => [2, 4, 7]]
        ];

        foreach ($journals as $journalValue) {
            $journal = Journal::factory()->create([
                'title' => $journalValue['title'],
                'user_id' => $journalValue['user_id']
            ]);

            foreach ($journalValue['categories'] as $category) {
                JournalCategory::query()->create(['journal_code' => $journal->code, 'category_id' => $category]);
            }
        }
    }
}

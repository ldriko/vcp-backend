<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $names = [
            'Pertanian',
            'Sains Data',
            'Hukum',
            'Pendidikan',
            'Ilmu Komunikasi',
            'Agama',
            'Ilmu Komputer',
            'Matematika',
            'Biologi',
            'Fisika'
        ];

        foreach ($names as $name) {
            Category::query()->create(['name' => $name]);
        }
    }
}

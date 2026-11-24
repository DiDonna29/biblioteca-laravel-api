<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Novela y Ficción'],
            ['name' => 'Ciencia Ficción y Fantasía'],
            ['name' => 'Historia y Biografías'],
            ['name' => 'Tecnología y Programación'],
            ['name' => 'Filosofía y Humanidades'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}

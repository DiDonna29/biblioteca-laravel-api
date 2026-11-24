<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fiction = Category::where('name', 'Novela y Ficción')->first();
        $scifi = Category::where('name', 'Ciencia Ficción y Fantasía')->first();
        $history = Category::where('name', 'Historia y Biografías')->first();
        $tech = Category::where('name', 'Tecnología y Programación')->first();
        $philosophy = Category::where('name', 'Filosofía y Humanidades')->first();

        $books = [
            [
                'title' => 'Cien años de soledad',
                'author' => 'Gabriel García Márquez',
                'description' => 'Obra cumbre del realismo mágico latinoamericano sobre la familia Buendía en Macondo.',
                'published_year' => 1967,
                'category_id' => $fiction?->id,
            ],
            [
                'title' => 'Don Quijote de la Mancha',
                'author' => 'Miguel de Cervantes',
                'description' => 'Las aventuras del hidalgo don Quijote y su escudero Sancho Panza.',
                'published_year' => 1605,
                'category_id' => $fiction?->id,
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'description' => 'Clásico distópico sobre la vigilancia estatal, el Gran Hermano y la manipulación de la verdad.',
                'published_year' => 1949,
                'category_id' => $scifi?->id,
            ],
            [
                'title' => 'Dune',
                'author' => 'Frank Herbert',
                'description' => 'Épica de ciencia ficción ambientada en el desértico planeta Arrakis.',
                'published_year' => 1965,
                'category_id' => $scifi?->id,
            ],
            [
                'title' => 'Sapiens: De animales a dioses',
                'author' => 'Yuval Noah Harari',
                'description' => 'Breve historia de la humanidad, desde los primeros homínidos hasta la revolución tecnológica.',
                'published_year' => 2014,
                'category_id' => $history?->id,
            ],
            [
                'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'author' => 'Robert C. Martin',
                'description' => 'Guía esencial de buenas prácticas, refactorización y principios SOLID en el desarrollo de software.',
                'published_year' => 2008,
                'category_id' => $tech?->id,
            ],
            [
                'title' => 'The Pragmatic Programmer',
                'author' => 'Andrew Hunt & David Thomas',
                'description' => 'Consejos atemporales para convertirse en un mejor desarrollador de software.',
                'published_year' => 1999,
                'category_id' => $tech?->id,
            ],
            [
                'title' => 'El mundo de Sofía',
                'author' => 'Jostein Gaarder',
                'description' => 'Una novela sobre la historia de la filosofía occidental contada a una joven estudiante.',
                'published_year' => 1991,
                'category_id' => $philosophy?->id,
            ],
        ];

        foreach ($books as $book) {
            if ($book['category_id']) {
                Book::firstOrCreate(
                    ['title' => $book['title'], 'author' => $book['author']],
                    $book
                );
            }
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_books(): void
    {
        $category = Category::factory()->create();
        Book::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'description',
                        'published_year',
                        'category_id',
                        'category' => ['id', 'name'],
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_can_filter_books_by_category(): void
    {
        $catSciFi = Category::factory()->create(['name' => 'Ciencia Ficción']);
        $catDrama = Category::factory()->create(['name' => 'Drama']);

        Book::factory()->count(2)->create(['category_id' => $catSciFi->id]);
        Book::factory()->count(3)->create(['category_id' => $catDrama->id]);

        // Filtrar exclusivamente por Ciencia Ficción
        $response = $this->getJson("/api/books?category={$catSciFi->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        foreach ($response->json('data') as $book) {
            $this->assertEquals($catSciFi->id, $book['category_id']);
        }
    }

    public function test_can_create_a_book_with_valid_data(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'title' => 'El Código Da Vinci',
            'author' => 'Dan Brown',
            'description' => 'Novela de misterio y conspiraciones religiosas.',
            'published_year' => 2003,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/books', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'El Código Da Vinci')
            ->assertJsonPath('data.author', 'Dan Brown')
            ->assertJsonPath('data.published_year', 2003)
            ->assertJsonPath('data.category_id', $category->id);

        $this->assertDatabaseHas('books', [
            'title' => 'El Código Da Vinci',
            'author' => 'Dan Brown',
            'category_id' => $category->id,
        ]);
    }

    public function test_cannot_create_book_with_title_shorter_than_3_characters(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'title' => 'AB', // Menos de 3 caracteres
            'author' => 'Autor Válido',
            'published_year' => 2020,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/books', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_cannot_create_book_without_required_fields(): void
    {
        $response = $this->postJson('/api/books', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'published_year', 'category_id']);
    }

    public function test_cannot_create_book_with_nonexistent_category(): void
    {
        $payload = [
            'title' => 'Título de Prueba',
            'author' => 'Autor de Prueba',
            'published_year' => 2022,
            'category_id' => 99999, // Categoría que no existe
        ];

        $response = $this->postJson('/api/books', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }

    public function test_can_show_a_book(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.title', $book->title);
    }

    public function test_returns_404_when_showing_nonexistent_book(): void
    {
        $response = $this->getJson('/api/books/99999');

        $response->assertStatus(404);
    }

    public function test_can_update_an_existing_book(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'Título Original',
            'author' => 'Autor Original',
            'published_year' => 2010,
        ]);

        $response = $this->putJson("/api/books/{$book->id}", [
            'title' => 'Título Editado',
            'published_year' => 2021,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Título Editado')
            ->assertJsonPath('data.published_year', 2021);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Título Editado',
            'published_year' => 2021,
        ]);
    }

    public function test_returns_404_when_updating_nonexistent_book(): void
    {
        $response = $this->putJson('/api/books/99999', [
            'title' => 'Cualquier Libro',
        ]);

        $response->assertStatus(404);
    }

    public function test_can_delete_an_existing_book(): void
    {
        $category = Category::factory()->create();
        $book = Book::factory()->create(['category_id' => $category->id]);

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Libro eliminado exitosamente.',
            ]);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_returns_404_when_deleting_nonexistent_book(): void
    {
        // Regla de negocio: Verificar la existencia del libro antes de proceder con su eliminación
        $response = $this->deleteJson('/api/books/99999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'El libro con ID 99999 que intentas eliminar no existe.',
            ]);
    }
}
